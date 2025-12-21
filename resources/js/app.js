import './bootstrap';

import 'bootstrap';

import Chart from 'chart.js/auto';

function renderSalesTrendChart(labels, values) {
	const canvas = document.getElementById('salesTrendChart');
	if (!canvas) return;

	// Destroy existing instance to avoid duplicates on Livewire re-render
	if (canvas.__chartInstance) {
		canvas.__chartInstance.destroy();
		canvas.__chartInstance = null;
	}

	if (!labels?.length) {
		return;
	}

	const ctx = canvas.getContext('2d');
	canvas.__chartInstance = new Chart(ctx, {
		type: 'line',
		data: {
			labels,
			datasets: [
				{
					label: 'Penjualan (Pemasukan)',
					data: values,
					tension: 0.35,
					borderWidth: 2,
					pointRadius: 3,
					fill: false,
				},
			],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					display: true,
					position: 'top',
				},
				tooltip: {
					callbacks: {
						label: (ctx) => {
							const v = Number(ctx.parsed.y ?? 0);
							return `${ctx.dataset.label}: Rp ${v.toLocaleString('id-ID')}`;
						},
					},
				},
			},
			scales: {
				y: {
					ticks: {
						callback: (value) => {
							const v = Number(value ?? 0);
							return `Rp ${v.toLocaleString('id-ID')}`;
						},
					},
				},
			},
		},
	});
}

window.addEventListener('sales-trend-chart', (event) => {
	const labels = event?.detail?.labels ?? [];
	const values = event?.detail?.values ?? [];
	renderSalesTrendChart(labels, values);
});

function renderAdminTokoPieChart(labels, values) {
	const canvas = document.getElementById('adminTokoPieChart');
	if (!canvas) return;

	if (canvas.__chartInstance) {
		canvas.__chartInstance.destroy();
		canvas.__chartInstance = null;
	}

	if (!labels?.length) return;

	const ctx = canvas.getContext('2d');
	canvas.__chartInstance = new Chart(ctx, {
		type: 'pie',
		data: {
			labels,
			datasets: [
				{
					label: 'Jumlah',
					data: values,
				},
			],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				tooltip: {
					callbacks: {
						label: (ctx) => {
							const v = Number(ctx.parsed ?? 0);
							return `${ctx.label}: ${v}`;
						},
					},
				},
			},
		},
	});
}

function renderAdminTokoBarChart(labels, values) {
	const canvas = document.getElementById('adminTokoBarChart');
	if (!canvas) return;

	if (canvas.__chartInstance) {
		canvas.__chartInstance.destroy();
		canvas.__chartInstance = null;
	}

	if (!labels?.length) return;

	const ctx = canvas.getContext('2d');
	canvas.__chartInstance = new Chart(ctx, {
		type: 'bar',
		data: {
			labels,
			datasets: [
				{
					label: 'Stok',
					data: values,
					borderWidth: 1,
				},
			],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: { display: true },
				tooltip: {
					callbacks: {
						label: (ctx) => `${ctx.dataset.label}: ${Number(ctx.parsed.y ?? 0)}`,
					},
				},
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						precision: 0,
					},
				},
			},
		},
	});
}

function renderAdminTokoDashboardCharts(detail) {
	const pie = detail?.pie;
	const bar = detail?.bar;

	if (pie) {
		renderAdminTokoPieChart(pie.labels ?? [], pie.values ?? []);
	}
	if (bar) {
		renderAdminTokoBarChart(bar.labels ?? [], bar.values ?? []);
	}
}

window.addEventListener('admin-toko-dashboard-charts', (event) => {
	renderAdminTokoDashboardCharts(event?.detail);
});

// Auto-render when Blade provides window.__adminTokoDashboardCharts
window.addEventListener('load', () => {
	const el = document.getElementById('adminTokoChartsData');
	const raw = el?.dataset?.charts;
	if (raw) {
		try {
			const parsed = JSON.parse(raw);
			renderAdminTokoDashboardCharts(parsed);
			return;
		} catch {
			// ignore
		}
	}

	if (window.__adminTokoDashboardCharts) {
		renderAdminTokoDashboardCharts(window.__adminTokoDashboardCharts);
	}
});
