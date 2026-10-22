<?php
// دالة لقراءة الفنادق
function getHotels() {
    $hotels = [];
    if(file_exists('data/hotels.txt')) {
        $lines = file('data/hotels.txt');
        foreach($lines as $line) {
            $parts = explode('|', trim($line));
            if(count($parts) >= 5) {
                $hotels[] = [
                    'id' => $parts[0],
                    'name' => $parts[1],
                    'city' => $parts[2],
                    'price' => $parts[3],
                    'image' => $parts[4]
                ];
            }
        }
    }
    return $hotels;
}

// الحصول على رقم الفندق
$hotel_id = isset($_GET['id']) ? $_GET['id'] : '1';

// البحث عن الفندق
$selected_hotel = null;
$hotels = getHotels();
foreach($hotels as $hotel) {
    if($hotel['id'] == $hotel_id) {
        $selected_hotel = $hotel;
        break;
    }
}

// إذا لم يوجد الفندق
if(!$selected_hotel) {
    echo "الفندق غير موجود";
    exit();
}

// معالجة الحجز
$booking_done = false;
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // جمع البيانات
    $guest_name = $_POST['guest_name'];
    $phone = $_POST['phone'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    
    // حساب عدد الليالي
    $nights = (strtotime($checkout) - strtotime($checkin)) / (60*60*24);
    $total_price = $nights * $selected_hotel['price'];
    
    // رقم الحجز
    $booking_number = 'BK' . date('Ymd') . rand(100, 999);
    
    // حفظ الحجز
    $booking_data = "$booking_number|$guest_name|$phone|{$selected_hotel['name']}|$checkin|$checkout|$nights|$total_price|" . date('Y-m-d H:i:s');
    
    $file = fopen('data/bookings.txt', 'a');
    fwrite($file, $booking_data . "\n");
    fclose($file);
    
    $booking_done = true;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز فندق <?php echo $selected_hotel['name']; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hotel-page {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .hotel-info {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .hotel-image {
            width: 100%;
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .booking-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .submit-btn {
            background: #1a237e;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
        }
        
        .booking-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        
        .booking-number {
            font-size: 20px;
            font-weight: bold;
            color: #1a237e;
            margin: 10px 0;
        }
        
        .back-link {
            display: inline-block;
            margin: 20px 0;
            color: #1a237e;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- شريط التنقل -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1>فنادق</h1>
            </div>
            <nav class="main-nav">
                <a href="index.php">الرئيسية</a>
                <a href="bookings.php">حجوزاتي</a>
                <a href="admin.php">لوحة التحكم</a>
            </nav>
        </div>
    </header>

    <!-- محتوى الصفحة -->
    <div class="hotel-page">
        <a href="index.php" class="back-link">← العودة للرئيسية</a>
        
        <?php if($booking_done): ?>
            <!-- رسالة نجاح الحجز -->
            <div class="booking-success">
                <h3>تم تأكيد الحجز بنجاح!</h3>
                <p>شكراً <?php echo $guest_name; ?></p>
                <div class="booking-number">رقم الحجز: <?php echo $booking_number; ?></div>
                <p>السعر الإجمالي: <?php echo $total_price; ?> ريال</p>
                <p>سيتم التواصل معك على <?php echo $phone; ?></p>
                <a href="bookings.php">عرض جميع الحجوزات</a>
            </div>
        <?php endif; ?>
        
        <!-- معلومات الفندق -->
        <div class="hotel-info">
            <h2><?php echo $selected_hotel['name']; ?></h2>
            <p><strong>المدينة:</strong> <?php echo $selected_hotel['city']; ?></p>
            <p><strong>السعر لليلة:</strong> <?php echo $selected_hotel['price']; ?> ريال</p>
            
            <div class="hotel-image">
                <?php
                $img_path = "images/" . $selected_hotel['image'] . ".jpg";
                if(file_exists($img_path)): ?>
                    <img src="<?php echo $img_path; ?>" alt="<?php echo $selected_hotel['name']; ?>">
                <?php else: ?>
                    <img src="images/default-hotel.jpg" alt="فندق">
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(!$booking_done): ?>
            <!-- نموذج الحجز -->
            <div class="booking-form">
                <h3>نموذج الحجز</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>اسمك الكامل</label>
                        <input type="text" name="guest_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>رقم الهاتف</label>
                        <input type="tel" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label>تاريخ الوصول</label>
                        <input  type="date" name="checkin" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>تاريخ المغادرة</label>
                        <input type="date" name="checkout" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    
                    <button type="submit" class="submit-btn">تأكيد الحجز</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- تذييل الصفحة -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3>نظام حجز الفنادق</h3>
                </div>
                <div class="footer-links">
                    <a href="index.php">الرئيسية</a>
                    <a href="bookings.php">حجوزاتي</a>
                    <a href="admin.php">لوحة التحكم</a>
                </div>
                <div class="footer-contact">
                    <p>الهاتف: 967717266653</p>
                </div>
            </div>
            <div class="copyright">
                <p>© 2026 نظام حجز الفنادق</p>
            </div>
        </div>
    </footer>
</body>
</html>