/**
 * نظام الإشعارات للوحة التحكم
 */

// إنشاء عنصر الإشعار
function createNotification(message, type = 'success', duration = 5000) {
    // إنشاء عنصر الإشعار
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    // إضافة أيقونة مناسبة حسب نوع الإشعار
    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="uil uil-check-circle"></i>';
            break;
        case 'error':
            icon = '<i class="uil uil-exclamation-triangle"></i>';
            break;
        case 'warning':
            icon = '<i class="uil uil-exclamation-circle"></i>';
            break;
        case 'info':
            icon = '<i class="uil uil-info-circle"></i>';
            break;
        default:
            icon = '<i class="uil uil-bell"></i>';
    }
    
    // إضافة محتوى الإشعار
    notification.innerHTML = `
        <div class="notification__icon">
            ${icon}
        </div>
        <div class="notification__content">
            <p>${message}</p>
        </div>
        <button class="notification__close">
            <i class="uil uil-times"></i>
        </button>
    `;
    
    // إضافة الإشعار إلى الصفحة
    const notificationsContainer = document.querySelector('.notifications-container');
    if (!notificationsContainer) {
        // إنشاء حاوية الإشعارات إذا لم تكن موجودة
        const container = document.createElement('div');
        container.className = 'notifications-container';
        document.body.appendChild(container);
        container.appendChild(notification);
    } else {
        notificationsContainer.appendChild(notification);
    }
    
    // إضافة حدث إغلاق الإشعار
    const closeBtn = notification.querySelector('.notification__close');
    closeBtn.addEventListener('click', () => {
        notification.classList.add('notification-closing');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
    
    // إغلاق الإشعار تلقائيًا بعد المدة المحددة
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('notification-closing');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, duration);
    
    // إضافة تأثير ظهور الإشعار
    setTimeout(() => {
        notification.classList.add('notification-show');
    }, 10);
    
    return notification;
}

// عرض إشعار نجاح
function showSuccessNotification(message, duration = 5000) {
    return createNotification(message, 'success', duration);
}

// عرض إشعار خطأ
function showErrorNotification(message, duration = 5000) {
    return createNotification(message, 'error', duration);
}

// عرض إشعار تحذير
function showWarningNotification(message, duration = 5000) {
    return createNotification(message, 'warning', duration);
}

// عرض إشعار معلومات
function showInfoNotification(message, duration = 5000) {
    return createNotification(message, 'info', duration);
}

// التحقق من وجود رسائل PHP وتحويلها إلى إشعارات
document.addEventListener('DOMContentLoaded', function() {
    // البحث عن عناصر الرسائل
    const successMessages = document.querySelectorAll('.alert-success');
    const errorMessages = document.querySelectorAll('.alert-danger');
    const warningMessages = document.querySelectorAll('.alert-warning');
    const infoMessages = document.querySelectorAll('.alert-info');
    
    // تحويل رسائل النجاح إلى إشعارات
    successMessages.forEach(message => {
        showSuccessNotification(message.textContent.trim());
        message.remove(); // إزالة الرسالة الأصلية
    });
    
    // تحويل رسائل الخطأ إلى إشعارات
    errorMessages.forEach(message => {
        showErrorNotification(message.textContent.trim());
        message.remove(); // إزالة الرسالة الأصلية
    });
    
    // تحويل رسائل التحذير إلى إشعارات
    warningMessages.forEach(message => {
        showWarningNotification(message.textContent.trim());
        message.remove(); // إزالة الرسالة الأصلية
    });
    
    // تحويل رسائل المعلومات إلى إشعارات
    infoMessages.forEach(message => {
        showInfoNotification(message.textContent.trim());
        message.remove(); // إزالة الرسالة الأصلية
    });
});

// إضافة دعم للنماذج لعرض إشعارات بعد الإرسال
document.addEventListener('DOMContentLoaded', function() {
    // البحث عن جميع النماذج في لوحة التحكم
    const adminForms = document.querySelectorAll('form[data-notify]');
    
    adminForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // الحصول على نوع الإشعار ورسالته من سمات النموذج
            const notifyType = this.getAttribute('data-notify-type') || 'success';
            const notifyAction = this.getAttribute('data-notify-action') || 'تم الإجراء';
            const notifyMessage = this.getAttribute('data-notify-message') || `${notifyAction} بنجاح`;
            
            // تخزين معلومات الإشعار في sessionStorage لعرضها بعد إعادة تحميل الصفحة
            sessionStorage.setItem('adminNotification', JSON.stringify({
                type: notifyType,
                message: notifyMessage
            }));
        });
    });
    
    // التحقق من وجود إشعار مخزن وعرضه
    const storedNotification = sessionStorage.getItem('adminNotification');
    if (storedNotification) {
        const notification = JSON.parse(storedNotification);
        createNotification(notification.message, notification.type);
        sessionStorage.removeItem('adminNotification'); // إزالة الإشعار بعد عرضه
    }
});
