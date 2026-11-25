import './bootstrap';
import 'bootstrap';
import $ from 'jquery';

window.$ = window.jQuery = $;

$(document).on('change', '#course_id', function () {
    const id = this.value;

    if (!id) {
        $('#availability')
            .removeClass('alert-success alert-danger')
            .addClass('alert-secondary')
            .text('Pilih kursus untuk melihat sisa kursi.');
        return;
    }

    $('#availability')
        .removeClass('alert-success alert-danger')
        .addClass('alert-secondary')
        .text('Memuat ketersediaan...');

    $.ajax({
        url: `/courses/${id}/availability`,
        method: 'GET',
        dataType: 'json',
        success(data) {
            $('#availability')
                .removeClass('alert-secondary alert-danger')
                .addClass('alert-success')
                .text(`${data.remaining} kursi tersisa untuk ${data.course}.`);
        },
        error() {
            $('#availability')
                .removeClass('alert-secondary alert-success')
                .addClass('alert-danger')
                .text('Gagal memuat ketersediaan kursus.');
        }
    });
});

/* ------------------------------------------------------------------
 * Pertemuan 13: live search Course (progressive enhancement)
 * Form GET di halaman Course index tetap bekerja tanpa JavaScript.
 * Kode di bawah hanya meningkatkan kenyamanan: hasil diganti lewat Ajax.
 * ----------------------------------------------------------------- */
let searchTimer = null;
let currentRequest = null;

function loadCourses(url, data) {
    // Batalkan request sebelumnya agar hasil lama tidak menimpa hasil baru.
    if (currentRequest) {
        currentRequest.abort();
    }

    $('#course-results').addClass('opacity-50');

    currentRequest = $.get(url, { ...data, partial: 1 })
        .done(function (html) {
            $('#course-results').replaceWith(html);

            // Samakan alamat browser dengan hasil yang tampil (tanpa partial=1),
            // sehingga reload/bookmark membuka hasil yang sama.
            const shown = new URL(this.url, window.location.origin);
            shown.searchParams.delete('partial');
            window.history.replaceState(null, '', shown.pathname + shown.search);
        })
        .fail(function (xhr, status) {
            if (status === 'abort') {
                return;
            }
            console.error('Gagal memuat hasil course.', status);
            $('#course-results')
                .removeClass('opacity-50')
                .prepend('<div class="alert alert-danger">Gagal memuat hasil. Coba klik tombol Cari.</div>');
        });
}

// Ketik di kolom cari: kirim request 300 ms setelah berhenti mengetik (debounce).
$(document).on('input', '#live-search', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
        loadCourses($('#course-filter').attr('action'), {
            q: $('#live-search').val(),
            status: $('#status-filter').val(),
        });
    }, 300);
});

// Ganti status: langsung muat ulang.
$(document).on('change', '#status-filter', function () {
    clearTimeout(searchTimer);
    loadCourses($('#course-filter').attr('action'), {
        q: $('#live-search').val(),
        status: $('#status-filter').val(),
    });
});

// Klik nomor halaman: ambil halaman itu lewat Ajax (href sudah membawa q & status).
$(document).on('click', '#course-results .pagination a', function (e) {
    e.preventDefault();
    loadCourses($(this).attr('href'), {});
});
