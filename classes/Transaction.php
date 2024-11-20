<?php
class Transaction {
    private $conn;
    private $fineRate = 1000; // Denda per hari

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllTransactions() {
        return $this->conn->query("SELECT transactions.*, books.title, members.name 
                                   FROM transactions
                                   JOIN books ON transactions.book_id = books.id
                                   JOIN members ON transactions.member_id = members.id");
    }

    public function borrowBook($book_id, $member_id, $borrow_date) {
        $query = "INSERT INTO transactions (book_id, member_id, borrow_date, status) 
                  VALUES ('$book_id', '$member_id', '$borrow_date', 'borrowed')";
        $this->conn->query($query);

        // Kurangi stok buku
        $this->conn->query("UPDATE books SET stock = stock - 1 WHERE id = $book_id");
    }

    public function returnBook($transaction_id, $return_date) {
        $query = "UPDATE transactions SET return_date = '$return_date', status = 'returned' 
                  WHERE id = $transaction_id";
        $this->conn->query($query);

        // Hitung denda
        $result = $this->conn->query("SELECT borrow_date FROM transactions WHERE id = $transaction_id");
        $row = $result->fetch_assoc();
        $borrow_date = new DateTime($row['borrow_date']);
        $return_date = new DateTime($return_date);
        $interval = $borrow_date->diff($return_date)->days;

        $fine = ($interval > 7) ? ($interval - 7) * $this->fineRate : 0;
        $this->conn->query("UPDATE transactions SET fine = $fine WHERE id = $transaction_id");

        // Tambahkan stok buku kembali
        $result = $this->conn->query("SELECT book_id FROM transactions WHERE id = $transaction_id");
        $row = $result->fetch_assoc();
        $book_id = $row['book_id'];
        $this->conn->query("UPDATE books SET stock = stock + 1 WHERE id = $book_id");
    }
}
?>
