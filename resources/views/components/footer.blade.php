<footer class="public-site-footer">
    <div class="public-footer-inner">
        <div class="public-footer-brand">
            <a href="{{ route('home') }}" class="public-brand" aria-label="Nivora home">
                <span class="public-brand-mark" aria-hidden="true">N</span>
                <span class="public-brand-name">NIVORA</span>
            </a>
            <p>Discover local businesses. Support local growth.</p>
            <span class="public-footer-note">Platform untuk menemukan dan mengenal bisnis lokal Indonesia.</span>
        </div>

        <nav class="public-footer-column" aria-label="Navigasi platform">
            <h2>Platform</h2>
            <a href="{{ route('explore') }}">Explore bisnis</a>
            <a href="{{ route('home') }}#categories">Kategori produk</a>
            <a href="{{ route('home') }}#recommendations">Produk pilihan</a>
        </nav>

        <nav class="public-footer-column" aria-label="Informasi Nivora">
            <h2>Nivora</h2>
            <a href="{{ route('home') }}#about">Tentang kami</a>
            <a href="{{ route('register') }}">Bergabung sebagai bisnis</a>
        </nav>

        <nav class="public-footer-column" aria-label="Akun dan bantuan">
            <h2>Akun</h2>
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}">Daftar member</a>
        </nav>
    </div>

    <div class="public-footer-bottom">
        <p>© {{ now()->year }} Nivora. Hak cipta dilindungi.</p>
        <a href="{{ route('home') }}">Dibuat untuk bisnis lokal <span aria-hidden="true">↗</span></a>
    </div>
</footer>
