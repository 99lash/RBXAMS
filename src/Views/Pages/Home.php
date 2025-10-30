<div class="p-6">
	<!-- Install Banner -->
	<div id="pwa-install-banner" class="bg-primary/10 border border-primary p-4 rounded-lg flex flex-col lg:flex-row justify-between items-center mb-6">
		<div class="flex items-center gap-4 mb-4 flex-wrap md:flex-nowrap">
			<div class="p-2 border border-dotted border-primary rounded-lg">
				<i data-lucide="line-chart" class="w-6 h-6 text-primary"></i>
			</div>
			<div>
				<h3 class="font-bold text-primary text-base sm:text-lg">Install RBXAMS</h3>
				<p class="text-info0 text-xs sm:text-sm">Get quick access to your Roblox Asset Monitoring System. Install it
					as an app for a better experience!</p>
			</div>
		</div>
		<div class="flex items-center gap-2">
			<button class="btn btn-sm btn-primary text-primary-content">Install App</button>
		</div>
	</div>

	<!-- Dashboard Header -->
	<div class="flex justify-between items-center mb-6">
		<h2 class="text-xl sm:text-2xl font-bold">Dashboard</h2>
		<div class="dropdown dropdown-end">
			<label tabindex="0" class="btn btn-sm btn-outline">
				<i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
				<span>Today</span>
				<i data-lucide="chevron-down" class="w-4 h-4 ml-2"></i>
			</label>
			<ul tabindex="0" id="period-options" class="dropdown-content menu p-2 shadow bg-base-200 rounded-box w-52">
				<li><a data-period="today">Today</a></li>
				<li><a data-period="week">This Week</a></li>
				<li><a data-period="month">This Month</a></li>
				<li><a data-period="quarter">This Quarter</a></li>
				<li><a data-period="year">This Year</a></li>
				<li><a data-period="all">All Time</a></li>
			</ul>
		</div>
	</div>

	<!-- Summary Cards -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
		<!-- Robux Bought -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl font-bold">Robux Bought</h2>
				<p id="robux-bought-pending" class="text-sm text-neutral/50">Pending: 0</p>
				<p id="robux-bought-fastflip" class="text-sm text-neutral/50">Fastflip: 0</p>
				<p id="robux-bought-total" class="text-base sm:text-lg font-semibold text-success/90">Total: 0</p>
			</div>
		</div>
		<!-- Robux Sold -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl font-bold">Robux Sold</h2>
				<p id="robux-sold-pending" class="text-sm text-neutral/50">Pending: 0</p>
				<p id="robux-sold-fastflip" class="text-sm text-neutral/50">Fastflip: 0</p>
				<p id="robux-sold-total" class="text-base sm:text-lg font-semibold text-success/90">Total: 0</p>
			</div>
		</div>
		<!-- Invested -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl font-bold">Invested</h2>
				<p id="invested-pending" class="text-sm text-neutral/50">Pending: P0.00</p>
				<p id="invested-fastflip" class="text-sm text-neutral/50">Fastflip: P0.00</p>
				<p id="invested-total" class="text-base sm:text-lg font-semibold text-success/90">Total: P0.00</p>
			</div>
		</div>
		<!-- Profit -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl font-bold">Profit</h2>
				<p id="profit-pending" class="text-sm text-neutral/50">Pending: P0.00</p>
				<p id="profit-fastflip" class="text-sm text-neutral/50">Fastflip: P0.00</p>
				<p id="profit-total" class="text-base sm:text-lg font-semibold text-success/90">Total: P0.00</p>
			</div>
		</div>
	</div>

	<!-- Charts Section -->
	<div class="grid grid-cols-1 grid-rows-1 lg:grid-rows-2 gap-6">
		<!-- Profit vs Expenses Chart -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl text-primary/80">Profit vs Expenses</h2>
				<p class="text-sm">Compare profit and expenses by account type</p>
				<canvas id="profit-chart"></canvas>
			</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
			<!-- Account Type Distribution Chart -->
			<div class="card bg-base-200 shadow-xl">
				<div class="card-body">
					<h2 class="card-title text-lg sm:text-xl text-primary/80">Account Type Distribution</h2>
					<p class="text-sm">Breakdown of accounts by type</p>
					<canvas id="type-chart"></canvas>
				</div>
			</div>

			<!-- Account Status Distribution Chart -->
			<div class="card bg-base-200 shadow-xl">
				<div class="card-body">
					<h2 class="card-title text-lg sm:text-xl text-primary/80">Account Status Distribution</h2>
					<p class="text-sm">Breakdown of accounts by status</p>
					<canvas id="status-chart"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="/scripts/dashboard.js"></script>