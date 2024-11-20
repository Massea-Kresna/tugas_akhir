<?php
class Member {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllMembers() {
        return $this->conn->query("SELECT * FROM members");
    }

    public function addMember($name, $email, $phone) {
        $query = "INSERT INTO members (name, email, phone) VALUES ('$name', '$email', $phone)";
        return $this->conn->query($query);
    }

    public function updateMember($id, $name, $email, $phone) {
        $query = "UPDATE members SET name='$name', email='$email', phone='$phone' WHERE id=$id";
        return $this->conn->query($query);
    }

    public function deleteMember($id) {
        return $this->conn->query("DELETE FROM members WHERE id=$id");
    }
}
?>
