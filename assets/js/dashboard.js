
// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    // Score Progress Chart
    const scoreCtx = document.getElementById('scoreChart').getContext('2d');
    const scoreChart = new Chart(scoreCtx, {
    type: 'line',
    data: {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Today'],
        datasets: [{
        label: 'Score',
        data: [400, 550, 600, 750, 850, 1000, 1250],
        borderColor: '#00ff88',
        backgroundColor: 'rgba(0, 255, 136, 0.1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
        legend: {
            display: false
        }
        },
        scales: {
        y: {
            beginAtZero: true,
            grid: {
            color: 'rgba(192, 252, 204, 0.1)'
            },
            ticks: {
            color: 'rgba(192, 252, 204, 0.7)'
            }
        },
        x: {
            grid: {
            color: 'rgba(192, 252, 204, 0.1)'
            },
            ticks: {
            color: 'rgba(192, 252, 204, 0.7)'
            }
        }
        }
    }
    });

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: ['Web', 'Crypto', 'Pwn', 'Reverse', 'Forensics', 'Misc'],
        datasets: [{
        data: [12, 8, 7, 6, 5, 4],
        backgroundColor: [
            'rgba(0, 255, 136, 0.7)',
            'rgba(0, 200, 255, 0.7)',
            'rgba(255, 0, 200, 0.7)',
            'rgba(255, 150, 0, 0.7)',
            'rgba(150, 0, 255, 0.7)',
            'rgba(0, 100, 255, 0.7)'
        ],
        borderColor: 'rgba(10, 10, 10, 0.8)',
        borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
        legend: {
            position: 'right',
            labels: {
            color: 'rgba(192, 252, 204, 0.7)',
            font: {
                family: "'Share Tech Mono', monospace"
            }
            }
        }
        },
        cutout: '70%'
    }
    });

    // Period buttons functionality
    document.querySelectorAll('.period-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelector('.period-btn.active').classList.remove('active');
        this.classList.add('active');
        
        // In a real app, this would fetch new data for the selected period
        // For demo, we'll just animate the chart
        scoreChart.data.datasets[0].data = getRandomData(this.textContent);
        scoreChart.update();
    });
    });

    // Simulate data change for different periods
    function getRandomData(period) {
    const base = [400, 550, 600, 750, 850, 1000, 1250];
    if (period === '7d') return base;
    if (period === '30d') return base.map(x => x * 1.5);
    if (period === '90d') return base.map(x => x * 3);
    return base.map(x => x * 5);
    }

    // Animate progress bars
    document.querySelectorAll('.achievement-progress-bar').forEach(bar => {
    const targetWidth = bar.style.width;
    bar.style.width = '0%';
    setTimeout(() => {
        bar.style.width = targetWidth;
    }, 500);
    });
});

// Toggle user dropdown (would be implemented fully in a real app)
function toggleDropdown() {
    console.log("User dropdown toggled - would show menu in production");
}
