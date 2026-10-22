<?php
// دالة لقراءة الفنادق من الملف
function getHotelsFromFile() {
    $hotels = [];
    
    if(file_exists('data/hotels.txt')) {
        $file = fopen('data/hotels.txt', 'r');
        
        while(!feof($file)) {
            $line = fgets($file);
            
            if(!empty(trim($line))) {
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
        
        fclose($file);
    }
    
    return $hotels;
}

$allHotels = getHotelsFromFile();

if(empty($allHotels)) {
    $allHotels = [
        [
            'id' => '1',
            'name' => 'فندق ماريوت',
            'city' => 'اب',
            'price' => '5000',
            'image' => 'hotel1'
        ],
        [
            'id' => '2', 
            'name' => ' الساحر',
            'city' => 'السبل',
            'price' => '3500',
            'image' => 'hotel2'
        ],
        [
            'id' => '3',
            'name' => 'فندق  الحرمين',
            'city' => 'الجولة',
            'price' => '6000', 
            'image' => 'hotel3'
        ],
        [
            'id' => '4',
            'name' => 'فندق  القصر',
            'city' => 'السبل',
            'price' => '4000',
            'image' => 'hotel4'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فنادق - حجز فنادق فاخرة</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- ==================== -->
    <!-- 1. شريط التنقل العلوي -->
    <!-- ==================== -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1>فنادق</h1>
            </div>
            
            <nav class="main-nav">
                <a href="index.php" class="active">الرئيسية</a>
                <a href="bookings.php">حجوزاتي</a>
                <a href="admin.php">لوحة التحكم</a>
            </nav>
        </div>
    </header>

    <!-- ==================== -->
    <!-- 2. قسم الترحيب -->
    <!-- ==================== -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h2>استمتع بإقامة فاخرة</h2>
                <p>اختر من بين أفضل الفنادق في اليمن</p>
                <a href="#hotels" class="cta-button">استعرض الفنادق</a>
            </div>
        </div>
    </section>

    <!-- ==================== -->
    <!-- 3. عرض الفنادق -->
    <!-- ==================== -->
    <section class="hotels-section" id="hotels">
        <div class="container">
            <h2 class="section-title">الفنادق المميزة</h2>
            
            <div class="hotels-grid">
                <?php foreach($allHotels as $hotel): ?>
                <div class="hotel-card">
                    <!-- صورة الفندق -->
                    <div class="hotel-image">
                        <?php
                        $image_path = "images/" . $hotel['image'] . ".jpg";
                        if(file_exists($image_path)): ?>
                            <img src="<?php echo $image_path; ?>" 
                                 alt="<?php echo $hotel['name']; ?>">
                        <?php else: ?>
                            <img src="images/default-hotel.jpg" 
                                 alt="صورة افتراضية">
                        <?php endif; ?>
                        <div class="hotel-price">
                            <?php echo $hotel['price']; ?> <span>ريال/ليلة</span>
                        </div>
                    </div>
                    
                    <!-- معلومات الفندق -->
                    <div class="hotel-info">
                        <div class="hotel-header">
                            <h3><?php echo $hotel['name']; ?></h3>
                        </div>
                        
                        <p class="hotel-location">
                            <?php echo $hotel['city']; ?>
                        </p>
                        
                        <p class="hotel-description">
                            فندق فاخر يوفر إقامة ممتعة مع جميع وسائل الراحة.
                        </p>
                        
                        <!-- زر الحجز -->
                        <a href="hotel.php?id=<?php echo $hotel['id']; ?>" 
                           class="book-button">احجز الآن</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ==================== -->
    <!-- 4. تذييل الصفحة -->
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
                    <p>الهاتف:967717266653 </p>
                    <p>البريد: omaralzomor20230@gmail.com</p>
                </div>
            </div>
            
            <div class="copyright">
                <p>© 2026 جميع الحقوق محفوظة | نظام حجز الفنادق</p>
            </div>
        </div>
    </footer>
</body>
</html>