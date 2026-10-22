<?php
// دالة لقراءة الحجوزات من الملف
function getBookingsFromFile() {
    $bookings = [];
    
    if(file_exists('data/bookings.txt')) {
        $file = fopen('data/bookings.txt', 'r');
        
        while(!feof($file)) {
            $line = fgets($file);
            
            if(!empty(trim($line))) {
                $parts = explode('|', trim($line));
                
                // البيانات الجديدة بعد التبسيط:
                // رقم_الحجز|اسم_الضيف|هاتف|اسم_الفندق|تاريخ_الوصل|تاريخ_المغادرة|عدد_الليالي|السعر|تاريخ_الحجز
                if(count($parts) >= 9) {
                    $bookings[] = [
                        'booking_number' => $parts[0],      // رقم الحجز
                        'guest_name' => $parts[1],          // اسم الضيف
                        'phone' => $parts[2],               // الهاتف
                        'hotel_name' => $parts[3],          // اسم الفندق
                        'checkin' => $parts[4],             // تاريخ الوصول
                        'checkout' => $parts[5],            // تاريخ المغادرة
                        'nights' => $parts[6],              // عدد الليالي
                        'total_price' => $parts[7],         // السعر الإجمالي
                        'booking_date' => $parts[8]         // تاريخ الحجز
                    ];
                }
            }
        }
        
        fclose($file);
    }
    
    return $bookings;
}

// دالة لحذف حجز
function deleteBooking($booking_number) {
    $bookings = getBookingsFromFile();
    $new_bookings = [];
    
    foreach($bookings as $booking) {
        if($booking['booking_number'] != $booking_number) {
            $new_bookings[] = $booking;
        }
    }
    
    // إعادة كتابة الملف بالحجوزات المتبقية
    $file = fopen('data/bookings.txt', 'w');
    foreach($new_bookings as $booking) {
        $line = implode('|', $booking) . "\n";
        fwrite($file, $line);
    }
    fclose($file);
}

// ============================================
// 2. معالجة البيانات
// ============================================

// جلب جميع الحجوزات
$allBookings = getBookingsFromFile();

// حذف الحجز
if(isset($_GET['delete'])) {
    $booking_to_delete = $_GET['delete'];
    deleteBooking($booking_to_delete);
    
    // إعادة التوجيه بعد الحذف
    header('Location: bookings.php?deleted=1');
    exit();
}

// رسالة النجاح بعد الحذف
$deleted_message = '';
if(isset($_GET['deleted'])) {
    $deleted_message = 'تم حذف الحجز بنجاح';
}

// ============================================
// 3. إحصائيات بسيطة
// ============================================
$total_bookings = count($allBookings);
$total_amount = 0;

foreach($allBookings as $booking) {
    $total_amount += intval($booking['total_price']);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجوزاتي - نظام حجز الفنادق</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* =========================================== */
        /* تنسيقات صفحة الحجوزات */
        /* =========================================== */
        
        .bookings-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #1a237e, #283593);
            color: white;
            border-radius: 12px;
        }
        
        .page-header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        /* =========================================== */
        /* إحصائيات الحجوزات */
        /* =========================================== */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            text-align: center;
            border-top: 4px solid #1a237e;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1a237e;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        
        /* =========================================== */
        /* بطاقات الحجوزات */
        /* =========================================== */
        .bookings-list {
            margin-top: 30px;
        }
        
        .booking-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-right: 5px solid #1a237e;
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .booking-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a237e;
        }
        
        .booking-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .detail-label {
            color: #666;
            font-size: 14px;
        }
        
        .detail-value {
            color: #333;
            font-weight: 600;
            font-size: 16px;
        }
        
        .detail-value.price {
            color: #1a237e;
            font-size: 20px;
            font-weight: 700;
        }
        
        /* =========================================== */
        /* أزرار الإجراءات */
        /* =========================================== */
        .booking-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        /* =========================================== */
        /* رسائل التنبيه */
        /* =========================================== */
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .no-bookings {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .no-bookings a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        
        /* =========================================== */
        /* تنسيقات للجوال */
        /* =========================================== */
        @media (max-width: 768px) {
            .bookings-page {
                padding: 15px;
            }
            
            .booking-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .booking-details-grid {
                grid-template-columns: 1fr;
            }
            
            .booking-actions {
                flex-direction: column;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- ==================== -->
    <!-- شريط التنقل العلوي -->
    <!-- ==================== -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1>فنادق</h1>
            </div>
            
            <nav class="main-nav">
                <a href="index.php">الرئيسية</a>
                <a href="bookings.php" class="active">حجوزاتي</a>
                <a href="admin.php">لوحة التحكم</a>
            </nav>
        </div>
    </header>

    <!-- ==================== -->
    <!-- محتوى صفحة الحجوزات -->
    <!-- ==================== -->
    <div class="bookings-page">
        <!-- رسائل التنبيه -->
        <?php if($deleted_message): ?>
            <div class="alert-success">
                ✅ <?php echo $deleted_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- ==================== -->
        <!-- رأس الصفحة -->
        <!-- ==================== -->
        <div class="page-header">
            <h1>📋 حجوزاتي</h1>
            <p>إدارة ومتابعة جميع حجوزاتك</p>
        </div>
        
        <?php if($total_bookings == 0): ?>
            <!-- ==================== -->
            <!-- حالة عدم وجود حجوزات -->
            <!-- ==================== -->
            <div class="no-bookings">
                <h3>لا توجد حجوزات حالياً</h3>
                <p>يمكنك البدء بحجز فندقك الأول الآن</p>
                <a href="index.php">استعرض الفنادق</a>
            </div>
        <?php else: ?>
            <!-- ==================== -->
            <!-- إحصائيات الحجوزات -->
            <!-- ==================== -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_bookings; ?></div>
                    <div class="stat-label">إجمالي الحجوزات</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($total_amount); ?> ريال</div>
                    <div class="stat-label">القيمة الإجمالية</div>
                </div>
            </div>
            
            <!-- ==================== -->
            <!-- عرض الحجوزات -->
            <!-- ==================== -->
            <div class="bookings-list">
                <?php foreach($allBookings as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <div class="booking-number">
                                رقم الحجز: <?php echo $booking['booking_number']; ?>
                            </div>
                            
                            <a href="bookings.php?delete=<?php echo $booking['booking_number']; ?>" 
                               class="btn-delete"
                               onclick="return confirm('هل تريد حذف الحجز رقم <?php echo $booking['booking_number']; ?>؟')">
                                🗑️ حذف الحجز
                            </a>
                        </div>
                        
                        <div class="booking-details-grid">
                            <!-- معلومات الحجز الأساسية -->
                            <div class="detail-item">
                                <span class="detail-label">اسم الضيف</span>
                                <span class="detail-value"><?php echo htmlspecialchars($booking['guest_name']); ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">رقم الهاتف</span>
                                <span class="detail-value"><?php echo $booking['phone']; ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">الفندق</span>
                                <span class="detail-value"><?php echo $booking['hotel_name']; ?></span>
                            </div>
                            
                            <!-- تاريخ الحجز -->
                            <div class="detail-item">
                                <span class="detail-label">تاريخ الحجز</span>
                                <span class="detail-value"><?php echo $booking['booking_date']; ?></span>
                            </div>
                            
                            <!-- معلومات الإقامة -->
                            <div class="detail-item">
                                <span class="detail-label">تاريخ الوصول</span>
                                <span class="detail-value"><?php echo $booking['checkin']; ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">تاريخ المغادرة</span>
                                <span class="detail-value"><?php echo $booking['checkout']; ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">مدة الإقامة</span>
                                <span class="detail-value"><?php echo $booking['nights']; ?> ليلة</span>
                            </div>
                            
                            <!-- السعر -->
                            <div class="detail-item">
                                <span class="detail-label">السعر الإجمالي</span>
                                <span class="detail-value price"><?php echo number_format($booking['total_price']); ?> ريال</span>
                            </div>
                        </div>
                        
                        <!-- معلومات إضافية -->
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                            <p style="margin: 0; color: #666; font-size: 14px;">
                                <strong>ملاحظة:</strong> تم إنشاء هذا الحجز في <?php echo $booking['booking_date']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- ==================== -->
        <!-- معلومات مساعدة -->
        <!-- ==================== -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 10px; margin-top: 40px; border: 1px solid #eaeaea;">
            <h3 style="color: #1a237e; margin-bottom: 15px;">معلومات مهمة:</h3>
            <div style="color: #666; line-height: 1.7;">
                <p><strong>لإلغاء الحجز:</strong> اضغط على زر "حذف الحجز" قبل 24 ساعة من تاريخ الوصول</p>
                <p><strong>للتواصل:</strong> هاتف: 967717266653</p>
                <p><strong>ساعات العمل:</strong> من الساعة 8 صباحاً إلى 10 مساءً</p>
            </div>
        </div>
    </div>

    <!-- ==================== -->
    <!-- تذييل الصفحة -->
    <!-- ==================== -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3>نظام حجز الفنادق</h3>
                    <p>أفضل حل لحجز الفنادق في اليمن</p>
                </div>
                
                <div class="footer-links">
                    <h4>روابط سريعة</h4>
                    <a href="index.php">الرئيسية</a>
                    <a href="bookings.php">حجوزاتي</a>
                    <a href="admin.php">لوحة التحكم</a>
                </div>
                
                <div class="footer-contact">
                    <h4>اتصل بنا</h4>
                    <p>الهاتف: 967717266653</p>
                    <p>البريد:  omaralzomor20230@gmail.com</p>
                </div>
            </div>
            
            <div class="copyright">
                <p>© 2026 جميع الحقوق محفوظة | نظام حجز الفنادق</p>
            </div>
        </div>
    </footer>
</body>
</html>