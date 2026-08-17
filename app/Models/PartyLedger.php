<?php
namespace App\Models;

use Core\Model;

class PartyLedger extends Model {

    /**
     * Fetch all ledger entries for a party, sorted by date.
     */
    public function getLedgerEntries(int $partyId, string $partyType): array {
        // 1. Fetch opening balances
        $stmt = $this->db->prepare("
            SELECT pob.*, je.journal_number, rje.journal_number AS reversal_journal_number
            FROM party_opening_balances pob
            LEFT JOIN journal_entries je ON pob.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON pob.reversal_journal_entry_id = rje.id
            WHERE pob.party_id = :party_id AND pob.status IN ('posted', 'reversed')
        ");
        $stmt->execute(['party_id' => $partyId]);
        $obRows = $stmt->fetchAll();

        // 2. Fetch posted/reversed customer receipts and supplier payments (Stage 5C)
        $stmt = $this->db->prepare("
            SELECT pr.*, je.journal_number, rje.journal_number AS reversal_journal_number
            FROM payment_receipts pr
            LEFT JOIN journal_entries je ON pr.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON pr.reversal_journal_entry_id = rje.id
            WHERE pr.party_id = :party_id AND pr.status IN ('posted', 'reversed')
        ");
        $stmt->execute(['party_id' => $partyId]);
        $prRows = $stmt->fetchAll();

        // 3. Fetch posted/cancelled marketplace credit invoices (Stage 6B)
        $stmt = $this->db->prepare("
            SELECT i.*, je.journal_number, rje.journal_number AS reversal_journal_number
            FROM invoices i
            LEFT JOIN journal_entries je ON i.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON i.reversal_journal_entry_id = rje.id
            WHERE i.customer_id = :party_id AND i.payment_type = 'CREDIT' AND i.status IN ('POSTED', 'CANCELLED')
        ");
        $stmt->execute(['party_id' => $partyId]);
        $saleRows = $stmt->fetchAll();

        $entries = [];

        // Map Opening Balances
        foreach ($obRows as $row) {
            $isReceivable = ($row['type'] === 'receivable');

            $entries[] = [
                'date' => $row['balance_date'],
                'reference' => $row['journal_number'] ?: 'OPB-' . $row['id'],
                'tx_type' => 'Opening Balance',
                'description' => $row['description'],
                'debit' => $isReceivable ? (float)$row['amount'] : 0.00,
                'credit' => !$isReceivable ? (float)$row['amount'] : 0.00,
                'timestamp' => strtotime($row['balance_date'] . ' 00:00:00') * 10 + $row['id']
            ];

            if ($row['status'] === 'reversed') {
                $entries[] = [
                    'date' => $row['updated_at'] ? date('Y-m-d', strtotime($row['updated_at'])) : $row['balance_date'],
                    'reference' => $row['reversal_journal_number'] ?: 'REV-' . $row['id'],
                    'tx_type' => 'Reversal',
                    'description' => 'Reversal: ' . ($row['reversal_reason'] ?: 'Correction entry'),
                    'debit' => !$isReceivable ? (float)$row['amount'] : 0.00,
                    'credit' => $isReceivable ? (float)$row['amount'] : 0.00,
                    'timestamp' => strtotime($row['updated_at'] ?? $row['balance_date']) * 10 + $row['id'] + 1
                ];
            }
        }

        // Map Receipts & Payments (Stage 5C)
        foreach ($prRows as $row) {
            $isReceipt = ($row['payment_type'] === 'RECEIPT');

            // Original Payment/Receipt Line
            $entries[] = [
                'date' => $row['payment_date'],
                'reference' => $row['journal_number'] ?: $row['payment_number'],
                'tx_type' => $isReceipt ? 'Receipt' : 'Payment',
                'description' => $row['notes'] ?: ($isReceipt ? 'Customer Receipt' : 'Supplier Payment'),
                // Customer Receipt -> Credit Customer
                // Supplier Payment -> Debit Supplier
                'debit' => !$isReceipt ? (float)$row['amount'] : 0.00,
                'credit' => $isReceipt ? (float)$row['amount'] : 0.00,
                'timestamp' => strtotime($row['payment_date'] . ' 00:00:00') * 10 + $row['id'] + 10000
            ];

            // Reversal Line
            if ($row['status'] === 'reversed') {
                $entries[] = [
                    'date' => $row['updated_at'] ? date('Y-m-d', strtotime($row['updated_at'])) : $row['payment_date'],
                    'reference' => $row['reversal_journal_number'] ?: 'REV-' . $row['payment_number'],
                    'tx_type' => 'Reversal',
                    'description' => 'Reversal: ' . ($row['reversal_reason'] ?: 'Correction entry'),
                    // Swap columns
                    'debit' => $isReceipt ? (float)$row['amount'] : 0.00,
                    'credit' => !$isReceipt ? (float)$row['amount'] : 0.00,
                    'timestamp' => strtotime($row['updated_at'] ?? $row['payment_date']) * 10 + $row['id'] + 10001
                ];
            }
        }

        // Map Credit Invoices (Stage 6B)
        foreach ($saleRows as $row) {
            $entries[] = [
                'date' => $row['invoice_date'],
                'reference' => $row['journal_number'] ?: $row['invoice_number'],
                'tx_type' => 'Invoice',
                'description' => $row['notes'] ?: 'Marketplace Credit Invoice',
                'debit' => (float)$row['total'],
                'credit' => 0.00,
                'timestamp' => strtotime($row['invoice_date'] . ' 00:00:00') * 10 + $row['id'] + 20000
            ];

            if ($row['status'] === 'CANCELLED') {
                $entries[] = [
                    'date' => $row['updated_at'] ? date('Y-m-d', strtotime($row['updated_at'])) : $row['invoice_date'],
                    'reference' => $row['reversal_journal_number'] ?: 'REV-' . $row['invoice_number'],
                    'tx_type' => 'Reversal',
                    'description' => 'Reversal: ' . ($row['reversal_reason'] ?: 'Invoice Cancelled'),
                    'debit' => 0.00,
                    'credit' => (float)$row['total'],
                    'timestamp' => strtotime($row['updated_at'] ?? $row['invoice_date']) * 10 + $row['id'] + 20001
                ];
            }
        }

        // Sort entries chronologically by timestamp
        usort($entries, function($a, $b) {
            return $a['timestamp'] <=> $b['timestamp'];
        });

        // Compute running balance
        $runningBalance = 0.00;
        $isCustomer = ($partyType === 'CUSTOMER' || $partyType === 'BOTH');

        foreach ($entries as &$entry) {
            if ($isCustomer) {
                // Customer: Debits increase, Credits decrease balance
                $runningBalance += $entry['debit'] - $entry['credit'];
            } else {
                // Supplier: Credits increase, Debits decrease balance
                $runningBalance += $entry['credit'] - $entry['debit'];
            }
            $entry['running_balance'] = $runningBalance;
        }

        return $entries;
    }

    /**
     * Compute current balance of a customer or supplier dynamically.
     */
    public function calculateBalance(int $partyId, string $partyType): float {
        $entries = $this->getLedgerEntries($partyId, $partyType);
        if (empty($entries)) {
            return 0.00;
        }
        return $entries[count($entries) - 1]['running_balance'];
    }

    /**
     * Get the net posted opening balance (non-reversed).
     */
    public function getOpeningBalance(int $partyId): float {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM party_opening_balances 
            WHERE party_id = :party_id AND status = 'posted'
        ");
        $stmt->execute(['party_id' => $partyId]);
        return (float)$stmt->fetchColumn();
    }
}
