        </main>
    </div>
</div>

<script>
function updateLiveClock() {
    const clockEl = document.getElementById('liveClockDisplay');
    if (!clockEl) return;
    const now = new Date();
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    
    const dayName = days[now.getDay()];
    const date = now.getDate();
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();
    
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    clockEl.textContent = `${dayName}, ${date} ${monthName} ${year} • ${hours}:${minutes}:${seconds} WIB`;
}

function toggleSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('mobileBackdrop');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
    }
    if (backdrop) {
        backdrop.classList.toggle('hidden');
    }
}

setInterval(updateLiveClock, 1000);
updateLiveClock();
</script>
</body>
</html>