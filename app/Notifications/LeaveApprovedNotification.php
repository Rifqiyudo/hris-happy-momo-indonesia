<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $leave;

    /**
     * Create a new notification instance.
     * Kita masukkan data Leave agar bisa diproses di email/database
     */
    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    /**
     * Get the notification's delivery channels.
     * Mengirim via Database (Dashboard) dan Mail (Opsional)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('employee.leave.show', $this->leave->id);

        return (new MailMessage)
            ->subject('Permohonan ' . $this->leave->type . ' Disetujui')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kabar baik! Permohonan ' . strtolower($this->leave->type) . ' Anda telah disetujui oleh pihak HRD.')
            ->line('Detail Pengajuan:')
            ->line('• Tipe: ' . $this->leave->type)
            ->line('• Tanggal: ' . $this->leave->start_date . ' s/d ' . $this->leave->end_date)
            ->action('Lihat Detail Pengajuan', $url)
            ->line('Terima kasih telah menggunakan aplikasi SILAKEN/Happy Momo!');
    }

    /**
     * Simpan data ke tabel 'notifications' di database
     * Data ini yang akan kita panggil di Dashboard Karyawan
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_id' => $this->leave->id,
            'title'    => 'Izin/Cuti Disetujui',
            'message'  => 'Permohonan ' . strtolower($this->leave->type) . ' Anda untuk tanggal ' . $this->leave->start_date . ' telah disetujui.',
            'icon'     => 'fas fa-check-circle',
            'type'     => 'success'
        ];
    }
}