<template>
  <article class="fc" :class="{ 'fc--unavailable': !product.is_available }" @click="navigate">
    <div class="fc__img-wrap">
      <img
        :src="product.image_url || '/images/placeholder-product.jpg'"
        :alt="product.name"
        class="fc__img"
      />
      <div class="fc__img-gradient"></div>

      <div class="fc__top-badges">
        <span v-if="product.category" class="fc__badge fc__badge--cat">
          {{ product.category.name }}
        </span>
        <span v-if="!product.is_available" class="fc__badge fc__badge--sold">
          Sold Out
        </span>
      </div>

      <div v-if="showRating" class="fc__rating">
        <svg class="fc__star" viewBox="0 0 16 16" fill="currentColor">
          <path d="M8 .25a.75.75 0 0 1 .673.418l1.882 3.815 4.21.612a.75.75 0 0 1 .416 1.279l-3.046 2.97.719 4.192a.751.751 0 0 1-1.088.791L8 12.347l-3.766 1.98a.75.75 0 0 1-1.088-.79l.72-4.194L.818 6.374a.75.75 0 0 1 .416-1.28l4.21-.611L7.327.668A.75.75 0 0 1 8 .25Z"/>
        </svg>
        <span>{{ (product.rating || 4.5).toFixed(1) }}</span>
      </div>
    </div>

    <div class="fc__body">
      <h3 class="fc__name">{{ product.name }}</h3>
      <p class="fc__desc">{{ product.description || 'A delicious dish crafted with care and the finest ingredients.' }}</p>

      <div class="fc__footer">
        <span class="fc__price">${{ formatPrice(product.price) }}</span>
        <button
          class="fc__btn"
          :disabled="!product.is_available"
          @click.stop="addToCart"
          :aria-label="`Add ${product.name} to cart`"
        >
          <svg class="fc__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          Add
        </button>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  product: { type: Object, required: true },
  showRating: { type: Boolean, default: true }
});

const emit = defineEmits(['add-to-cart']);

const navigate = () => {
  router.visit(route('web.products.show', { product: props.product.id }));
};

const formatPrice = (price) => {
  const n = typeof price === 'number' ? price : parseFloat(price);
  return n.toFixed(2);
};

const addToCart = () => emit('add-to-cart', props.product);
</script>

<style scoped>
:global(.v-theme--dark) .fc {
  --ink: #F0EDE8;
  --muted: #9A9490;
  --surface: #1A1F2E;
  --border: rgba(255, 255, 255, 0.08);
  --shadow: rgba(0, 0, 0, 0.3);
  --shadow-hover: rgba(201, 98, 47, 0.22);
}

:global(.v-theme--dark) .fc__badge--cat {
  background: rgba(26, 31, 46, 0.88);
  color: #F0EDE8;
}

:global(.v-theme--dark) .fc__rating {
  background: rgba(26, 31, 46, 0.92);
  color: #F0EDE8;
}

:global(.v-theme--dark) .fc__btn {
  background: #F0EDE8;
  color: #1C1917;
}

:global(.v-theme--dark) .fc__btn:hover:not(:disabled) {
  background: #C9622F;
  color: #FFFCF9;
}

.fc {
  --accent: #C9622F;
  --accent-light: #f0e6df;
  --sage: #4A7C59;
  --ink: #1C1917;
  --muted: #78716C;
  --surface: #FFFCF9;
  --border: #EDE8E3;
  --shadow: rgba(28, 25, 23, 0.09);
  --shadow-hover: rgba(201, 98, 47, 0.18);

  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--surface);
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid var(--border);
  box-shadow: 0 2px 12px var(--shadow);
  cursor: pointer;
  display: flex;
  flex-direction: column;
  height: 100%;
  transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1),
              box-shadow 0.28s ease,
              border-color 0.28s ease;
  position: relative;
  will-change: transform;
}

.fc:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px var(--shadow-hover), 0 4px 16px var(--shadow);
  border-color: rgba(201, 98, 47, 0.25);
}

.fc--unavailable {
  opacity: 0.72;
}

/* Image */
.fc__img-wrap {
  position: relative;
  height: 210px;
  overflow: hidden;
  flex-shrink: 0;
}

.fc__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  display: block;
}

.fc:hover .fc__img {
  transform: scale(1.07);
}

.fc__img-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    transparent 40%,
    rgba(28, 25, 23, 0.42) 100%
  );
  pointer-events: none;
}

/* Badges */
.fc__top-badges {
  position: absolute;
  top: 12px;
  left: 12px;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.fc__badge {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 10.5px;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 20px;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.fc__badge--cat {
  background: rgba(255, 252, 249, 0.88);
  color: var(--ink);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.fc__badge--sold {
  background: rgba(201, 98, 47, 0.9);
  color: #fff;
}

/* Rating */
.fc__rating {
  position: absolute;
  bottom: 12px;
  right: 12px;
  display: flex;
  align-items: center;
  gap: 4px;
  background: rgba(255, 252, 249, 0.92);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  border-radius: 20px;
  padding: 4px 10px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 12px;
  font-weight: 700;
  color: var(--ink);
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.fc__star {
  width: 11px;
  height: 11px;
  color: #E8962E;
  flex-shrink: 0;
}

/* Body */
.fc__body {
  padding: 18px 20px 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: 0;
}

.fc__name {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 18px;
  font-weight: 600;
  color: var(--ink);
  line-height: 1.25;
  margin: 0 0 7px;
  letter-spacing: -0.01em;
}

.fc__desc {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.55;
  margin: 0 0 16px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

/* Footer */
.fc__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
}

.fc__price {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 22px;
  font-weight: 700;
  color: var(--accent);
  letter-spacing: -0.02em;
  line-height: 1;
}

.fc__btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--ink);
  color: #FFFCF9;
  border: none;
  border-radius: 40px;
  padding: 9px 18px 9px 14px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.18s ease, box-shadow 0.2s ease;
  letter-spacing: 0.01em;
}

.fc__btn:hover:not(:disabled) {
  background: var(--accent);
  transform: scale(1.04);
  box-shadow: 0 4px 14px rgba(201, 98, 47, 0.4);
}

.fc__btn:active:not(:disabled) {
  transform: scale(0.97);
}

.fc__btn:disabled {
  background: #D6D0CA;
  color: #A89F9A;
  cursor: not-allowed;
}

.fc__btn-icon {
  width: 15px;
  height: 15px;
  flex-shrink: 0;
}
</style>
