<?php
if (isset($_GET['id'])) {
    $id_sinhvien = $_GET['id']; // Lấy ID từ URL

    // Kiểm tra nếu ID hợp lệ (là số và lớn hơn 0)
    if (!is_numeric($id_sinhvien) || intval($id_sinhvien) <= 0) {
        die("ID không hợp lệ.");
    }

    // Chuyển ID sang kiểu số nguyên
    $id_sinhvien = intval($id_sinhvien);

    // Kết nối đến cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'qlsv_nguyenquanghuy(21108)');
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Câu lệnh SQL để xóa sinh viên
    $delete_sql = "DELETE FROM table_Students WHERE id = ?";

    // Chuẩn bị câu lệnh SQL
    $stmt = $conn->prepare($delete_sql);
    if ($stmt === false) {
        die("Lỗi chuẩn bị câu lệnh SQL: " . $conn->error);
    }

    // Liên kết biến id_sinhvien với tham số trong câu lệnh SQL
    $stmt->bind_param("i", $id_sinhvien);

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        // Xóa thành công
        echo "Sinh viên đã được xóa thành công.";
        
        // Đảm bảo không có nội dung nào được gửi trước khi chuyển hướng
        if (!headers_sent()) {
            header("Location: index.php");
            exit(); // Dừng mã sau khi chuyển hướng
        }
    } else {
        // Xóa thất bại
        echo "Lỗi khi xóa sinh viên: " . $stmt->error;
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();
} else {
    echo "Không có ID sinh viên để xóa.";
}
?>
