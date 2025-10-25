document.addEventListener('DOMContentLoaded', () => {
	// Profit vs Expenses Chart
	const profitCtx = document.getElementById('profit-chart');
	if (profitCtx) {
		new Chart(profitCtx, {
			type: 'bar',
			data: {
				labels: ['Pending', 'Fastflip'],
				datasets: [{
					label: 'Profit',
					data: [12, 19], // Example data
					backgroundColor: 'rgba(75, 192, 192, 0.2)',
					borderColor: 'rgba(75, 192, 192, 1)',
					borderWidth: 1
				}, {
					label: 'Expenses',
					data: [5, 10], // Example data
					backgroundColor: 'rgba(255, 99, 132, 0.2)',
					borderColor: 'rgba(255, 99, 132, 1)',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				// maintainAspectRatio: false,
				scales: {
					y: {
						beginAtZero: true
					}
				}
			}
		});
	}

	// Account Type Distribution Chart
	const typeCtx = document.getElementById('type-chart');
	if (typeCtx) {
		new Chart(typeCtx, {
			type: 'doughnut',
			data: {
				labels: ['Pending Accounts', 'Fastflip Accounts'],
				datasets: [{
					label: 'Account Type',
					data: [1, 1], // Example data
					backgroundColor: [
						'rgba(153, 102, 255, 0.2)',
						'rgba(75, 192, 192, 0.2)'
					],
					borderColor: [
						'rgba(153, 102, 255, 1)',
						'rgba(75, 192, 192, 1)'
					],
					borderWidth: 1
				}]
			}
		});
	}

	// Account Status Distribution Chart
	const statusCtx = document.getElementById('status-chart');
	if (statusCtx) {
		new Chart(statusCtx, {
			type: 'pie',
			data: {
				labels: ['Sold', 'Unpend', 'Pending', 'Retrieved'],
				datasets: [{
					label: 'Account Status',
					data: [1, 0, 1], // Example data
					backgroundColor: [
						'rgba(75, 192, 192, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 205, 86, 0.2)'
					],
					borderColor: [
						'rgba(75, 192, 192, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255, 205, 86, 1)'
					],
					borderWidth: 1
				}]
			}
		});
	}
});
