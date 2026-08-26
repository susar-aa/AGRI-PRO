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

        // 3. Fetch all marketplace invoices (Cash/Bank/Credit) (Stage 6B)
        $stmt = $this->db->prepare("
            SELECT i.*, je.journal_number, rje.journal_number AS reversal_journal_number
            FROM invoices i
            LEFT JOIN journal_entries je ON i.journal_entry_id = je.id
            LEFT JOIN journal_entries rje ON i.reversal_journal_entry_id = rje.id
            WHERE i.customer_id = :party_id AND i.status IN ('POSTED', 'CANCELLED')
        ");
        $stmt->execute(['party_id' => $partyId]);
        $saleRows = $stmt->fetchAll();

        // 4. Fetch Member Fees
        $stmt = $this->db->prepare("
            SELECT m.*, je.journal_number 
            FROM coop_members m 
            LEFT JOIN journal_entries je ON m.journal_entry_id = je.id 
            WHERE m.party_id = :party_id AND (m.registration_fee > 0 OR m.shares_fee > 0)
        ");
        $stmt->execute(['party_id' => $partyId]);
        $memberRows = $stmt->fetchAll();

        // 5. Fetch Fixed Deposits
        $stmt = $this->db->prepare("
            SELECT fd.*, je.journal_number 
            FROM member_fixed_deposits fd 
            JOIN coop_members m ON fd.member_id = m.id 
            LEFT JOIN journal_entries je ON fd.journal_entry_id = je.id 
            WHERE m.party_id = :party_id AND fd.status = 'ACTIVE'
        ");
        $stmt->execute(['party_id' => $partyId]);
        $fdRows = $stmt->fetchAll();

        // 6. Fetch GRNs for Suppliers
        $grnRows = [];
        if (in_array($partyType, ['SUPPLIER', 'BOTH'])) {
            $stmt = $this->db->prepare("
                SELECT sl.*, p.name_en AS product_name
                FROM stock_ledger sl
                LEFT JOIN products p ON sl.product_id = p.id
                WHERE sl.movement_type = 'GRN' 
                AND sl.source_module = 'marketplace'
                AND sl.source_transaction_id = :party_id
            ");
            $stmt->execute(['party_id' => $partyId]);
            $grnRows = $stmt->fetchAll();
        }

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

        // Map Invoices (Stage 6B)
        foreach ($saleRows as $row) {
            $isCredit = ($row['payment_type'] === 'CREDIT');

            $entries[] = [
                'date' => $row['invoice_date'],
                'reference' => $row['journal_number'] ?: $row['invoice_number'],
                'tx_type' => 'Invoice (' . $row['payment_type'] . ')',
                'description' => $row['notes'] ?: 'Marketplace Invoice',
                'debit' => (float)$row['total'],
                'credit' => $isCredit ? 0.00 : (float)$row['total'],
                'timestamp' => strtotime($row['invoice_date'] . ' 00:00:00') * 10 + $row['id'] + 20000
            ];

            if ($row['status'] === 'CANCELLED') {
                $entries[] = [
                    'date' => $row['updated_at'] ? date('Y-m-d', strtotime($row['updated_at'])) : $row['invoice_date'],
                    'reference' => $row['reversal_journal_number'] ?: 'REV-' . $row['invoice_number'],
                    'tx_type' => 'Reversal',
                    'description' => 'Reversal: ' . ($row['reversal_reason'] ?: 'Invoice Cancelled'),
                    'debit' => $isCredit ? 0.00 : (float)$row['total'],
                    'credit' => (float)$row['total'],
                    'timestamp' => strtotime($row['updated_at'] ?? $row['invoice_date']) * 10 + $row['id'] + 20001
                ];
            }
        }

        // Map GRNs
        foreach ($grnRows as $row) {
            $total = (float)$row['quantity_in'] * (float)$row['unit_cost'];
            $entries[] = [
                'date' => date('Y-m-d', strtotime($row['movement_date'])),
                'reference' => $row['reference_number'] ?: 'GRN-' . $row['id'],
                'tx_type' => 'Goods Receipt',
                'description' => 'Received stock: ' . $row['product_name'],
                'debit' => 0.00,
                'credit' => $total,
                'timestamp' => strtotime($row['movement_date'] . ' 00:00:00') * 10 + $row['id'] + 25000
            ];
        }

        // Map Member Fees
        foreach ($memberRows as $row) {
            $totalFee = (float)$row['registration_fee'] + (float)$row['shares_fee'];
            $entries[] = [
                'date' => $row['registration_date'],
                'reference' => $row['journal_number'] ?: 'MEM-REG-' . $row['id'],
                'tx_type' => 'Registration & Shares',
                'description' => 'Member Registration and Share Fees',
                'debit' => $totalFee,
                'credit' => $totalFee,
                'timestamp' => strtotime($row['registration_date'] . ' 00:00:00') * 10 + $row['id'] + 30000
            ];
        }

        // Map Fixed Deposits
        foreach ($fdRows as $row) {
            $principal = (float)$row['maturity_amount'] - (float)$row['expected_interest'];
            $entries[] = [
                'date' => $row['start_date'],
                'reference' => $row['journal_number'] ?: $row['deposit_number'],
                'tx_type' => 'Fixed Deposit',
                'description' => 'FD Principal Deposit',
                'debit' => $principal,
                'credit' => $principal,
                'timestamp' => strtotime($row['start_date'] . ' 00:00:00') * 10 + $row['id'] + 40000
            ];
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
