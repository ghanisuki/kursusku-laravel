{{-- Tombol Lihat/Sembunyi password. Cara pakai: <button data-toggle-password="#id-input"> --}}
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.querySelector(btn.getAttribute('data-toggle-password'));
            if (!input) { return; }
            var sedangTersembunyi = input.type === 'password';
            input.type = sedangTersembunyi ? 'text' : 'password';
            btn.textContent = sedangTersembunyi ? 'Sembunyi' : 'Lihat';
        });
    });
</script>
