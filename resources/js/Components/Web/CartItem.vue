<template>
  <div class="ci">
    <div class="ci__img-wrap">
      <img
        :src="item.product.image_url || '/images/placeholder-product.jpg'"
        :alt="item.product.name"
        class="ci__img"
      />
    </div>

    <div class="ci__content">
      <div class="ci__header">
        <h3 class="ci__name">{{ item.product.name }}</h3>
        <button class="ci__delete" @click="removeItem" aria-label="Remove item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
            <path d="M10 11v6M14 11v6"></path>
            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
          </svg>
        </button>
      </div>

      <p class="ci__unit">${{ formatPrice(item.product.price) }} each</p>

      <div class="ci__footer">
        <div class="ci__stepper">
          <button
            class="ci__step-btn"
            @click="decrementQuantity"
            :disabled="item.quantity <= 1"
            aria-label="Decrease quantity"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
          </button>
          <span class="ci__qty">{{ item.quantity }}</span>
          <button
            class="ci__step-btn"
            @click="incrementQuantity"
            :disabled="item.quantity >= item.product.stock"
            aria-label="Increase quantity"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
          </button>
        </div>

        <span class="ci__subtotal">${{ formatPrice(subtotal) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  item: { type: Object, required: true }
});

const emit = defineEmits(['update:quantity', 'remove']);

const subtotal = computed(() => props.item.product.price * props.item.quantity);

const formatPrice = (price) => {
  const n = typeof price === 'number' ? price : parseFloat(price);
  return n.toFixed(2);
};

const incrementQuantity = () => emit('update:quantity', props.item.id, props.item.quantity + 1);
const decrementQuantity = () => {
  if (props.item.quantity > 1) emit('update:quantity', props.item.id, props.item.quantity - 1);
};
const removeItem = () => emit('remove', props.item.id);
</script>

<style scoped>
.ci {
  --accent: #C9622F;
  --ink: #1C1917;
  --muted: #78716C;
  --surface: #FFFCF9;
  --border: #EDE8E3;
  --danger: #C0392B;

  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 14px;
  display: flex;
  gap: 14px;
  align-items: flex-start;
  margin-bottom: 10px;
  transition: box-shadow 0.22s ease, transform 0.22s ease;
  box-shadow: 0 1px 6px rgba(28, 25, 23, 0.05);
}

.ci:hover {
  box-shadow: 0 6px 20px rgba(28, 25, 23, 0.09);
  transform: translateY(-2px);
}

/* Image */
.ci__img-wrap {
  width: 82px;
  height: 82px;
  border-radius: 12px;
  overflow: hidden;
  flex-shrink: 0;
  background: #EDE8E3;
}

.ci__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.3s ease;
}

.ci:hover .ci__img {
  transform: scale(1.06);
}

/* Content */
.ci__content {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.ci__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
}

.ci__name {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 16px;
  font-weight: 600;
  color: var(--ink);
  margin: 0;
  line-height: 1.25;
  letter-spacing: -0.01em;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.ci__unit {
  font-size: 12.5px;
  color: var(--muted);
  margin: 0;
  font-weight: 500;
}

/* Footer */
.ci__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 8px;
}

/* Stepper */
.ci__stepper {
  display: flex;
  align-items: center;
  gap: 0;
  background: #F5F0EB;
  border-radius: 40px;
  padding: 3px;
  border: 1px solid var(--border);
}

.ci__step-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: none;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--ink);
  transition: background 0.18s ease, color 0.18s ease, transform 0.15s ease;
  padding: 0;
}

.ci__step-btn svg {
  width: 14px;
  height: 14px;
}

.ci__step-btn:hover:not(:disabled) {
  background: var(--ink);
  color: #FFFCF9;
  transform: scale(1.1);
}

.ci__step-btn:disabled {
  color: #C5BCB7;
  cursor: not-allowed;
}

.ci__qty {
  width: 32px;
  text-align: center;
  font-family: 'Fraunces', Georgia, serif;
  font-size: 17px;
  font-weight: 700;
  color: var(--ink);
  line-height: 1;
  letter-spacing: -0.02em;
}

/* Subtotal */
.ci__subtotal {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 20px;
  font-weight: 700;
  color: var(--accent);
  letter-spacing: -0.02em;
}

/* Delete */
.ci__delete {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  border: 1px solid transparent;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted);
  flex-shrink: 0;
  padding: 0;
  transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
}

.ci__delete svg {
  width: 15px;
  height: 15px;
}

.ci__delete:hover {
  background: #FEF2F2;
  color: var(--danger);
  border-color: #FECACA;
}
</style>
