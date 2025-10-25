<div class="p-6">
	<!-- Install Banner -->
	<div class="bg-primary/10 border border-primary p-4 rounded-lg flex justify-between items-center mb-6">
		<div class="flex items-center gap-4">
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
			<button class="btn btn-sm btn-primary">Install App</button>
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
			<ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-200 rounded-box w-52">
				<li><a>Today</a></li>
				<li><a>This Week</a></li>
				<li><a>This Month</a></li>
				<li><a>Last Quarter</a></li>
				<li><a>This Year</a></li>
				<li><a>All Time</a></li>
			</ul>
		</div>
	</div>

	<!-- Summary Cards -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
		<!-- Robux Bought -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl">Robux Bought</h2>
				<p class="text-sm">Pending: 0</p>
				<p class="text-sm">Fastflip: 0</p>
				<p class="text-base sm:text-lg font-bold">Total: 0</p>
			</div>
		</div>
		<!-- Robux Sold -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl">Robux Sold</h2>
				<p class="text-sm">Pending: 0</p>
				<p class="text-sm">Fastflip: 0</p>
				<p class="text-base sm:text-lg font-bold">Total: 0</p>
			</div>
		</div>
		<!-- Invested -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl">Invested</h2>
				<p class="text-sm">Pending: P0.00</p>
				<p class="text-sm">Fastflip: P0.00</p>
				<p class="text-base sm:text-lg font-bold">Total: P0.00</p>
			</div>
		</div>
		<!-- Profit -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl">Profit</h2>
				<p class="text-sm">Pending: P0.00</p>
				<p class="text-sm">Fastflip: P0.00</p>
				<p class="text-base sm:text-lg font-bold">Total: P0.00</p>
			</div>
		</div>
	</div>

	<!-- Charts Section -->
	<div class="grid grid-cols-1 grid-rows-1 lg:grid-rows-2 gap-6">
		<!-- Profit vs Expenses Chart -->
		<div class="card bg-base-200 shadow-xl">
			<div class="card-body">
				<h2 class="card-title text-lg sm:text-xl">Profit vs Expenses</h2>
				<p class="text-sm">Compare profit and expenses by account type</p>
				<canvas id="profit-chart"></canvas>
			</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
			<!-- Account Type Distribution Chart -->
			<div class="card bg-base-200 shadow-xl">
				<div class="card-body">
					<h2 class="card-title text-lg sm:text-xl">Account Type Distribution</h2>
					<p class="text-sm">Breakdown of accounts by type</p>
					<canvas id="type-chart"></canvas>
				</div>
			</div>

			<!-- Account Status Distribution Chart -->
			<div class="card bg-base-200 shadow-xl">
				<div class="card-body">
					<h2 class="card-title text-lg sm:text-xl">Account Status Distribution</h2>
					<p class="text-sm">Breakdown of accounts by status</p>
					<canvas id="status-chart"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="/scripts/dashboard.js"></script>