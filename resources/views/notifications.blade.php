<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/notifications.blade.php -->
@extends('layouts.app')

@section('styles')
<style>
    .notification-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, opacity 0.3s;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .notification-card:hover {
        transform: translateY(-3px);
    }

    .notification-card.read {
        opacity: 0.7;
    }

    .notification-card.unread {
        border-left: 4px solid #6a11cb;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .icon-comment {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }

    .icon-vote {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .icon-system {
        background: linear-gradient(to right, #f46b45, #eea849);
    }

    .page-title {
        color: #4e54c8;
        font-weight: 700;
        border-bottom: 3px solid #4e54c8;
        display: inline-block;
        padding-bottom: 5px;
    }

    .action-btn {
        border-radius: 20px;
        padding: 5px 15px;
    }

    .unread-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #6a11cb;
        border: 2px solid white;
    }

    .tab-button {
        padding: 0.5rem 1rem;
        border-radius: 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
        margin-right: 0.5rem;
        font-size: 0.9rem;
    }

    .tab-button.active {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .notification-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Notifikasi</h1>

        <div>
            <button type="button" class="btn btn-outline-primary action-btn" id="markAllRead">
                <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
            </button>
        </div>
    </div>

    <div class="mb-4">
        <div class="btn-group">
            <button type="button" class="btn tab-button active" data-filter="all">
                Semua <span class="badge rounded-pill bg-secondary ms-1">12</span>
            </button>
            <button type="button" class="btn tab-button" data-filter="unread">
                Belum Dibaca <span class="badge rounded-pill bg-primary ms-1">5</span>
            </button>
            <button type="button" class="btn tab-button" data-filter="comments">
                Komentar <span class="badge rounded-pill bg-secondary ms-1">6</span>
            </button>
            <button type="button" class="btn tab-button" data-filter="votes">
                Votes <span class="badge rounded-pill bg-secondary ms-1">4</span>
            </button>
            <button type="button" class="btn tab-button" data-filter="system">
                Sistem <span class="badge rounded-pill bg-secondary ms-1">2</span>
            </button>
        </div>
    </div>

    <div class="notifications-container">
        <!-- Comment notification - unread -->
        <div class="notification-card p-3 unread" data-type="comments" data-read="false">
            <div class="d-flex">
                <div class="position-relative me-3">
                    <div class="notification-icon icon-comment">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <div class="unread-badge"></div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Ahmad Zaky mengomentari thread kamu</h6>
                            <p class="mb-1 text-muted">"Pertanyaan ini sangat menarik, saya juga pernah mengalami masalah serupa..."</p>
                            <p class="mb-0 small text-primary">
                                <a href="#" class="text-decoration-none">Tips Belajar Laravel untuk Pemula</a>
                            </p>
                        </div>
                        <div class="notification-time">5 menit yang lalu</div>
                    </div>
                    <div class="d-flex mt-2 justify-content-end gap-2">
                        <button class="btn btn-sm btn-light action-btn mark-read-btn">
                            <i class="fas fa-check me-1"></i>Tandai Dibaca
                        </button>
                        <a href="#" class="btn btn-sm btn-primary action-btn">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vote notification - unread -->
        <div class="notification-card p-3 unread" data-type="votes" data-read="false">
            <div class="d-flex">
                <div class="position-relative me-3">
                    <div class="notification-icon icon-vote">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="unread-badge"></div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Budi Santoso menyukai thread kamu</h6>
                            <p class="mb-1 text-muted">Thread kamu mendapatkan 1 upvote baru</p>
                            <p class="mb-0 small text-primary">
                                <a href="#" class="text-decoration-none">Cara Mengoptimalkan Database MySQL</a>
                            </p>
                        </div>
                        <div class="notification-time">15 menit yang lalu</div>
                    </div>
                    <div class="d-flex mt-2 justify-content-end gap-2">
                        <button class="btn btn-sm btn-light action-btn mark-read-btn">
                            <i class="fas fa-check me-1"></i>Tandai Dibaca
                        </button>
                        <a href="#" class="btn btn-sm btn-primary action-btn">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System notification - unread -->
        <div class="notification-card p-3 unread" data-type="system" data-read="false">
            <div class="d-flex">
                <div class="position-relative me-3">
                    <div class="notification-icon icon-system">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="unread-badge"></div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Selamat! Thread kamu mencapai 100 views</h6>
                            <p class="mb-1 text-muted">Thread kamu mendapatkan banyak perhatian dari komunitas</p>
                            <p class="mb-0 small text-primary">
                                <a href="#" class="text-decoration-none">Tips Belajar Laravel untuk Pemula</a>
                            </p>
                        </div>
                        <div class="notification-time">1 jam yang lalu</div>
                    </div>
                    <div class="d-flex mt-2 justify-content-end gap-2">
                        <button class="btn btn-sm btn-light action-btn mark-read-btn">
                            <i class="fas fa-check me-1"></i>Tandai Dibaca
                        </button>
                        <a href="#" class="btn btn-sm btn-primary action-btn">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comment notification - read -->
        <div class="notification-card p-3 read" data-type="comments" data-read="true">
            <div class="d-flex">
                <div class="position-relative me-3">
                    <div class="notification-icon icon-comment">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Dewi Sartika mengomentari thread kamu</h6>
                            <p class="mb-1 text-muted">"Terima kasih atas tips yang berguna ini. Saya baru saja menerapkannya..."</p>
                            <p class="mb-0 small text-primary">
                                <a href="#" class="text-decoration-none">Framework JavaScript Terbaik 2025</a>
                            </p>
                        </div>
                        <div class="notification-time">1 hari yang lalu</div>
                    </div>
                    <div class="d-flex mt-2 justify-content-end gap-2">
                        <a href="#" class="btn btn-sm btn-primary action-btn">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vote notification - read -->
        <div class="notification-card p-3 read" data-type="votes" data-read="true">
            <div class="d-flex">
                <div class="position-relative me-3">
                    <div class="notification-icon icon-vote">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">3 orang menyukai komentar kamu</h6>
                            <p class="mb-1 text-muted">"Saran yang sangat membantu, terima kasih telah berbagi pengalaman."</p>
                            <p class="mb-0 small text-primary">
                                <a href="#" class="text-decoration-none">Pengalaman Pertama Dengan Docker</a>
                            </p>
                        </div>
                        <div class="notification-time">2 hari yang lalu</div>
                    </div>
                    <div class="d-flex mt-2 justify-content-end gap-2">
                        <a href="#" class="btn btn-sm btn-primary action-btn">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <button type="button" class="btn btn-outline-secondary">
            <i class="fas fa-sync me-1"></i>Muat Notifikasi Lama
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab filtering
        const tabButtons = document.querySelectorAll('.tab-button');
        const notifications = document.querySelectorAll('.notification-card');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                tabButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');

                // Show/hide notifications based on filter
                notifications.forEach(notification => {
                    if (filter === 'all') {
                        notification.style.display = 'block';
                    } else if (filter === 'unread') {
                        notification.style.display = notification.getAttribute('data-read') === 'false' ? 'block' : 'none';
                    } else {
                        notification.style.display =
                            notification.getAttribute('data-type') === filter ? 'block' : 'none';
                    }
                });
            });
        });

        // Mark as read functionality
        const markReadButtons = document.querySelectorAll('.mark-read-btn');
        markReadButtons.forEach(button => {
            button.addEventListener('click', function() {
                const notificationCard = this.closest('.notification-card');
                notificationCard.classList.remove('unread');
                notificationCard.classList.add('read');
                notificationCard.setAttribute('data-read', 'true');

                // Remove unread badge
                const badge = notificationCard.querySelector('.unread-badge');
                if (badge) badge.remove();

                // Remove the mark as read button
                this.remove();

                // Here you would also want to make an AJAX call to mark the notification as read in the database
            });
        });

        // Mark all as read functionality
        document.getElementById('markAllRead').addEventListener('click', function() {
            const unreadNotifications = document.querySelectorAll('.notification-card.unread');
            unreadNotifications.forEach(notification => {
                notification.classList.remove('unread');
                notification.classList.add('read');
                notification.setAttribute('data-read', 'true');

                // Remove unread badge
                const badge = notification.querySelector('.unread-badge');
                if (badge) badge.remove();

                // Remove the mark as read button
                const markReadBtn = notification.querySelector('.mark-read-btn');
                if (markReadBtn) markReadBtn.remove();
            });

            // Here you would also make an AJAX call to mark all notifications as read
        });
    });
</script>
@endpush
