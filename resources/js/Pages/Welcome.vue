<template>
  <AppLayout>
    <Head title="Welcome" />

    <!-- Hero Slider Section -->
    <HeroSlider />

    <!-- Featured Products Section -->
    <FeaturedProducts />

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-glow cta-glow--top"></div>
      <div class="cta-glow cta-glow--bottom"></div>
      <div class="cta-content text-center">
        <span class="cta-eyebrow">GET STARTED TODAY</span>
        <h2 class="cta-title">Ready to Order?</h2>
        <p class="cta-sub">
          Join thousands of satisfied customers who enjoy our delicious food delivery service.
        </p>
        <div class="cta-actions">
          <a href="/register" class="cta-btn cta-btn--primary">
            <v-icon size="17">mdi-account-plus</v-icon>
            Get Started
          </a>
          <a href="/web/products" class="cta-btn cta-btn--ghost">
            <v-icon size="17">mdi-silverware-fork-knife</v-icon>
            View Menu
          </a>
        </div>
      </div>
    </section>

  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FeaturedProducts from '@/Components/Public/FeaturedProducts.vue';
import HeroSlider from '@/Components/Public/HeroSlider.vue';
</script>

<style scoped>
/* ── CTA Section ── */
.cta-section {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #1C1917 0%, #2d1b0e 50%, #161410 100%);
  padding: 96px 24px;
}

.cta-glow {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
}

.cta-glow--top {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(201, 98, 47, 0.18) 0%, transparent 70%);
  top: -180px;
  right: -120px;
}

.cta-glow--bottom {
  width: 350px;
  height: 350px;
  background: radial-gradient(circle, rgba(74, 124, 89, 0.14) 0%, transparent 70%);
  bottom: -120px;
  left: -80px;
}

.cta-content {
  position: relative;
  z-index: 1;
  max-width: 580px;
  margin: 0 auto;
}

.cta-eyebrow {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #C9622F;
  margin-bottom: 18px;
}

.cta-title {
  font-family: 'Fraunces', Georgia, serif;
  font-size: clamp(34px, 5vw, 54px);
  font-weight: 700;
  color: white;
  letter-spacing: -0.025em;
  line-height: 1.1;
  margin-bottom: 18px;
}

.cta-sub {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.55);
  max-width: 420px;
  margin: 0 auto 40px;
  line-height: 1.65;
}

.cta-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 32px;
  border-radius: 40px;
  font-size: 15px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.22s ease;
  cursor: pointer;
  font-family: inherit;
}

.cta-btn--primary {
  background: #C9622F;
  color: white;
  border: 2px solid #C9622F;
}

.cta-btn--primary:hover {
  background: #b8521f;
  border-color: #b8521f;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(201, 98, 47, 0.45);
}

.cta-btn--ghost {
  background: transparent;
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.25);
}

.cta-btn--ghost:hover {
  border-color: rgba(255, 255, 255, 0.7);
  background: rgba(255, 255, 255, 0.06);
}
</style>
