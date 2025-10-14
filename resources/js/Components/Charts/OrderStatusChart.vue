<template>
  <div class="order-status-chart">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {
  Chart,
  ArcElement,
  Tooltip,
  Legend,
  Title
} from 'chart.js';

// Register Chart.js components
Chart.register(ArcElement, Tooltip, Legend, Title);

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  },
  options: {
    type: Object,
    default: () => ({})
  }
});

const chartCanvas = ref(null);
let chartInstance = null;

const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          size: 12,
          weight: '500'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: 'white',
      bodyColor: 'white',
      borderColor: 'rgba(255, 255, 255, 0.2)',
      borderWidth: 1,
      cornerRadius: 8,
      displayColors: true,
      titleFont: {
        size: 14,
        weight: 'bold'
      },
      bodyFont: {
        size: 13
      },
      callbacks: {
        title: function(context) {
          return `Status: ${context[0].label}`;
        },
        label: function(context) {
          const total = context.dataset.data.reduce((a, b) => a + b, 0);
          const percentage = ((context.parsed / total) * 100).toFixed(1);
          return `${context.parsed} orders (${percentage}%)`;
        }
      }
    }
  },
  cutout: '60%',
  rotation: -90,
  circumference: 360,
  animation: {
    animateRotate: true,
    animateScale: false,
    duration: 1200,
    easing: 'easeInOutQuart'
  }
};

const createChart = () => {
  if (!chartCanvas.value || !props.data.length) return;

  // Destroy existing chart
  if (chartInstance) {
    chartInstance.destroy();
  }

  const ctx = chartCanvas.value.getContext('2d');
  
  // Prepare chart data
  const labels = props.data.map(item => item.name);
  const data = props.data.map(item => item.count);
  const backgroundColors = props.data.map(item => getStatusColor(item.name));
  const borderColors = props.data.map(item => getStatusColor(item.name, true));

  chartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: backgroundColors,
        borderColor: borderColors,
        borderWidth: 2,
        hoverOffset: 8,
        hoverBackgroundColor: backgroundColors.map(color => lightenColor(color, 20)),
        hoverBorderColor: borderColors.map(color => lightenColor(color, 20))
      }]
    },
    options: {
      ...defaultOptions,
      ...props.options
    }
  });
};

const getStatusColor = (status, isBorder = false) => {
  const colors = {
    'Pending': isBorder ? '#FF9800' : 'rgba(255, 152, 0, 0.8)',
    'Preparing': isBorder ? '#2196F3' : 'rgba(33, 150, 243, 0.8)',
    'Ready': isBorder ? '#9C27B0' : 'rgba(156, 39, 176, 0.8)',
    'Delivered': isBorder ? '#4CAF50' : 'rgba(76, 175, 80, 0.8)',
    'Cancelled': isBorder ? '#F44336' : 'rgba(244, 67, 54, 0.8)'
  };
  return colors[status] || (isBorder ? '#9E9E9E' : 'rgba(158, 158, 158, 0.8)');
};

const lightenColor = (color, percent) => {
  // Simple color lightening function
  if (color.startsWith('rgba')) {
    const values = color.match(/\d+/g);
    if (values && values.length >= 3) {
      const r = Math.min(255, parseInt(values[0]) + percent);
      const g = Math.min(255, parseInt(values[1]) + percent);
      const b = Math.min(255, parseInt(values[2]) + percent);
      return `rgba(${r}, ${g}, ${b}, 0.9)`;
    }
  }
  return color;
};

// Watch for data changes
watch(() => props.data, () => {
  createChart();
}, { deep: true });

onMounted(() => {
  createChart();
});

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>

<style scoped>
.order-status-chart {
  position: relative;
  height: 300px;
  width: 100%;
}

canvas {
  max-height: 300px;
}

/* Dark mode adjustments */
.dark .order-status-chart canvas {
  filter: brightness(0.9);
}
</style>
