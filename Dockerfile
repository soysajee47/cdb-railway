FROM php:8.2-apache

# ติดตั้ง Extension PDO และ MySQL สำหรับเชื่อมต่อฐานข้อมูล
RUN docker-php-ext-install pdo pdo_mysql mysqli

# เปิดใช้งาน Apache Rewrite Module
RUN a2enmod rewrite

# คัดลอกไฟล์ทั้งหมดในโปรเจกต์ไปยัง Web Root ของ Apache
COPY . /var/www/html/

# ปรับพอร์ต Apache ให้ตรงกับ $PORT ของ Railway
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80