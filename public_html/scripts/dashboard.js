document.addEventListener('DOMContentLoaded', () => {
	const profitChartElement = document.getElementById('profit-chart');
	const typeChartElement = document.getElementById('type-chart');
	const statusChartElement = document.getElementById('status-chart');

	let profitChart, typeChart, statusChart;

	const initializeCharts = () => {
		if (profitChartElement) {
			profitChart = new Chart(profitChartElement, {
				type: 'bar',
				data: { labels: [], datasets: [] },
				options: {
					responsive: true,
					scales: { y: { beginAtZero: true } }
				}
			});
		}
		if (typeChartElement) {
			typeChart = new Chart(typeChartElement, {
				type: 'doughnut',
				data: { labels: [], datasets: [] },
				options: { responsive: true }
			});
		}
		if (statusChartElement) {
			statusChart = new Chart(statusChartElement, {
				type: 'pie',
				data: { labels: [], datasets: [] },
				options: { responsive: true }
			});
		}
	};

	const fetchData = async (period = 'today') => {
		try {
			const response = await fetch(`/api/dashboard?period=${period}`);
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			const data = await response.json();
			// console.log(data);
			updateDashboard(data);
		} catch (error) {
			console.error('Failed to fetch dashboard data:', error);
			// Optionally, display an error message to the user
		}
	};

	const updateDashboard = (data) => {
		updateSummaryCards(data.summary);
		updateProfitChart(data.summary);
		updateTypeChart(data.accountTypeDistribution);
		updateStatusChart(data.accountStatusDistribution);
	};

	const formatCurrency = (amount) => `P${parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
	const formatNumber = (amount) => parseFloat(amount || 0).toLocaleString('en-US');

	const updateSummaryCards = (summary) => {
		const {
			total_pending_robux_bought, total_fastflip_robux_bought,
			total_pending_robux_sold, total_fastflip_robux_sold,
			total_pending_expenses_php, total_fastflip_expenses_php,
			total_pending_profit_php, total_fastflip_profit_php
		} = summary;

		const totalBought = parseFloat(total_pending_robux_bought) + parseFloat(total_fastflip_robux_bought);
		document.querySelector('#robux-bought-pending').textContent = `Pending: ${formatNumber(total_pending_robux_bought)}`;
		document.querySelector('#robux-bought-fastflip').textContent = `Fastflip: ${formatNumber(total_fastflip_robux_bought)}`;
		document.querySelector('#robux-bought-total').textContent = `Total: ${formatNumber(totalBought)}`;

		const totalSold = parseFloat(total_pending_robux_sold) + parseFloat(total_fastflip_robux_sold);
		document.querySelector('#robux-sold-pending').textContent = `Pending: ${formatNumber(total_pending_robux_sold)}`;
		document.querySelector('#robux-sold-fastflip').textContent = `Fastflip: ${formatNumber(total_fastflip_robux_sold)}`;
		document.querySelector('#robux-sold-total').textContent = `Total: ${formatNumber(totalSold)}`;

		const totalInvested = parseFloat(total_pending_expenses_php) + parseFloat(total_fastflip_expenses_php);
		document.querySelector('#invested-pending').textContent = `Pending: ${formatCurrency(total_pending_expenses_php)}`;
		document.querySelector('#invested-fastflip').textContent = `Fastflip: ${formatCurrency(total_fastflip_expenses_php)}`;
		document.querySelector('#invested-total').textContent = `Total: ${formatCurrency(totalInvested)}`;

		const totalProfit = parseFloat(total_pending_profit_php) + parseFloat(total_fastflip_profit_php);
		document.querySelector('#profit-pending').textContent = `Pending: ${formatCurrency(total_pending_profit_php)}`;
		document.querySelector('#profit-fastflip').textContent = `Fastflip: ${formatCurrency(total_fastflip_profit_php)}`;
		document.querySelector('#profit-total').textContent = `Total: ${formatCurrency(totalProfit)}`;
	};

	const updateProfitChart = (summary) => {
		if (!profitChart) return;
		const {
			total_pending_profit_php, total_fastflip_profit_php,
			total_pending_expenses_php, total_fastflip_expenses_php
		} = summary;

		profitChart.data.labels = ['Pending Accounts', 'Fastflip Accounts'];
		profitChart.data.datasets = [
			{
				label: 'Profit',
				data: [total_pending_profit_php, total_fastflip_profit_php],
				backgroundColor: 'rgba(75, 192, 192, 0.2)',
				borderColor: 'rgba(75, 192, 192, 1)',
				borderWidth: 1
			},
			{
				label: 'Expenses',
				data: [total_pending_expenses_php, total_fastflip_expenses_php],
				backgroundColor: 'rgba(255, 99, 132, 0.2)',
				borderColor: 'rgba(255, 99, 132, 1)',
				borderWidth: 1
			}
		];
		profitChart.update();
	};

	const updateTypeChart = (dist) => {
		if (!typeChart) return;
		// const labels = dist.map(item => item.account_type);
		const data = dist.map(item => item.count);
		typeChart.data.labels = ['Fastflip Accounts', 'Pending Accounts'];
		/* 
			it should be display the two types of accounts even if it's zero by count.
			This is hard because I don't have account_type table to get all the types. But we have a fixed type of accounts: Fastflip and Pending
		*/
		typeChart.data.datasets = [{
			label: 'Account Type',
			data: data,
			backgroundColor: ['rgba(153, 102, 255, 0.2)', 'rgba(75, 192, 192, 0.2)'],
			borderColor: ['rgba(153, 102, 255, 1)', 'rgba(75, 192, 192, 1)'],
			borderWidth: 1
		}];
		typeChart.update();
	};

	const updateStatusChart = (dist) => {
		if (!statusChart) return;
		const labels = dist.map(item => item.status);
		const data = dist.map(item => item.count);
		statusChart.data.labels = labels;
		/* 
			it should be display all the status of accounts even if it's zero by count.
			This is easy because we have account_status table to get all the account_status
		*/
		statusChart.data.datasets = [{
			label: 'Account Status',
			data: data,
			backgroundColor: [
				'rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)',
				'rgba(255, 205, 86, 0.2)', 'rgba(54, 162, 235, 0.2)'
			],
			borderColor: [
				'rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)',
				'rgba(255, 205, 86, 1)', 'rgba(54, 162, 235, 1)'
			],
			borderWidth: 1
		}];
		statusChart.update();
	};

	const periodDropdown = document.querySelector('#period-options');

	if (periodDropdown) {

		periodDropdown.addEventListener('click', (e) => {

			// alert('Dropdown clicked');

			if (e.target.tagName === 'A' && e.target.closest('.dropdown-content')) {

				const period = e.target.getAttribute('data-period');

				document.querySelector('.dropdown > label > span').textContent = e.target.textContent;

				fetchData(period);

			}

		});

	} initializeCharts();
	fetchData(); // Initial data fetch

	// PWA Install Banner Logic
	const pwaInstallBanner = document.getElementById('pwa-install-banner');
	const installButton = pwaInstallBanner ? pwaInstallBanner.querySelector('button') : null;
	let deferredPrompt;

	// Check if already installed on page load or from localStorage
	let isAppInstalled = (navigator.standalone || window.matchMedia('(display-mode: standalone)').matches || localStorage.getItem('pwaInstalled') === 'true');

	if (pwaInstallBanner) {
		if (isAppInstalled) {
			pwaInstallBanner.style.display = 'none';
		}

		// Only add the beforeinstallprompt listener if the app is not installed
		if (!isAppInstalled) {
			window.addEventListener('beforeinstallprompt', (e) => {
				e.preventDefault();
				deferredPrompt = e;
				pwaInstallBanner.style.display = 'flex'; // Show the banner
			});
		}

		if (installButton) {
			installButton.addEventListener('click', () => {
				pwaInstallBanner.style.display = 'none'; // Hide the banner
				if (deferredPrompt) {
					deferredPrompt.prompt();
					deferredPrompt.userChoice.then((choiceResult) => {
						if (choiceResult.outcome === 'accepted') {
							console.log('User accepted the A2HS prompt');
							localStorage.setItem('pwaInstalled', 'true'); // Persist installation status
						} else {
							console.log('User dismissed the A2HS prompt');
						}
						deferredPrompt = null;
					});
				}
			});
		}

		window.addEventListener('appinstalled', (e) => {
			console.log(e);
			console.log('meow');
			isAppInstalled = true; // Update the flag
			localStorage.setItem('pwaInstalled', 'true'); // Persist installation status
			pwaInstallBanner.style.display = 'none'; // Hide the banner once installed
		});
	}

});
