<?php
class Book {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBooks() {
        return $this->conn->query("SELECT * FROM books");
    }

    // Method baru untuk pencarian buku
    public function searchBooks($keyword) {
        $keyword = '%' . $this->conn->real_escape_string($keyword) . '%';
        $query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR year LIKE ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sss', $keyword, $keyword, $keyword);
        $stmt->execute();
        return $stmt->get_result();
    }


    public function addBook($title, $author, $year, $stock) {
        $query = "INSERT INTO books (title, author, year, stock) VALUES ('$title', '$author', '$year', '$stock')";
        return $this->conn->query($query);
    }

    public function updateBook($id, $title, $author, $year, $stock) {
        $query = "UPDATE books SET title='$title', author='$author', year='$year', stock='$stock' WHERE id=$id";
        return $this->conn->query($query);
    }

    public function deleteBook($id) {
        return $this->conn->query("DELETE FROM books WHERE id=$id");
    }
}
?>