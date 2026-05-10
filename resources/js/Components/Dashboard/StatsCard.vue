<template>
  <div class="sc" :class="`sc--${variant}`">
    <div class="sc__glow"></div>
    <div class="sc__inner">
      <div class="sc__top">
        <span class="sc__label">{{ title }}</span>
        <div class="sc__icon-wrap">
          <v-icon size="18" class="sc__icon">{{ icon }}</v-icon>
        </div>
      </div>
      <div class="sc__value">{{ value }}</div>
      <div v-if="subtitle" class="sc__sub">
        <span class="sc__sub-dot"></span>
        {{ subtitle }}
      </div>
    </div>
    <div class="sc__bar-track">
      <div class="sc__bar-fill"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  subtitle: { type: String, default: '' },
  icon: { type: String, default: 'mdi-information' },
  color: { type: String, default: 'primary' },
  variant: { type: String, default: 'default' }
});
</script>

<style scoped>
:global(.v-theme--dark) .sc {
  --c-bg: #1A1F2E;
  --c-border: rgba(255, 255, 255, 0.08);
  --c-ink: #F0EDE8;
  --c-muted: #9A9490;
}

:global(.v-theme--dark) .sc--revenue { --c-accent-glow: rgba(74,124,89,0.18); }
:global(.v-theme--dark) .sc--orders  { --c-accent-glow: rgba(42,110,187,0.18); }
:global(.v-theme--dark) .sc--products{ --c-accent-glow: rgba(124,77,170,0.18); }
:global(.v-theme--dark) .sc--stock   { --c-accent-glow: rgba(201,98,47,0.18); }
:global(.v-theme--dark) .sc--default { --c-accent-glow: rgba(201,98,47,0.18); }

.sc {
  --c-bg: #FFFCF9;
  --c-border: #EDE8E3;
  --c-ink: #1C1917;
  --c-muted: #78716C;
  --c-accent: #C9622F;
  --c-accent-glow: rgba(201, 98, 47, 0.12);
  --c-bar: #C9622F;

  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--c-bg);
  border: 1px solid var(--c-border);
  border-radius: 20px;
  padding: 22px 24px 0;
  position: relative;
  overflow: hidden;
  cursor: default;
  transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1),
              box-shadow 0.28s ease;
  box-shadow: 0 2px 10px rgba(28, 25, 23, 0.06);
}

.sc:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 36px rgba(28, 25, 23, 0.1), 0 4px 12px rgba(28, 25, 23, 0.06);
}

/* Variant color tokens */
.sc--revenue { --c-accent: #4A7C59; --c-accent-glow: rgba(74, 124, 89, 0.1); --c-bar: #4A7C59; }
.sc--orders  { --c-accent: #2A6EBB; --c-accent-glow: rgba(42, 110, 187, 0.1); --c-bar: #2A6EBB; }
.sc--products{ --c-accent: #7C4DAA; --c-accent-glow: rgba(124, 77, 170, 0.1); --c-bar: #7C4DAA; }
.sc--stock   { --c-accent: #C9622F; --c-accent-glow: rgba(201, 98, 47, 0.12); --c-bar: #C9622F; }
.sc--default { --c-accent: #C9622F; --c-accent-glow: rgba(201, 98, 47, 0.12); --c-bar: #C9622F; }

/* Glow blob behind icon area */
.sc__glow {
  position: absolute;
  top: -30px;
  right: -30px;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: var(--c-accent-glow);
  filter: blur(24px);
  pointer-events: none;
}

.sc__inner {
  position: relative;
  padding-bottom: 20px;
}

.sc__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.sc__label {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--c-muted);
}

.sc__icon-wrap {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: var(--c-accent-glow);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.sc__icon {
  color: var(--c-accent) !important;
}

.sc__value {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 34px;
  font-weight: 700;
  color: var(--c-ink);
  letter-spacing: -0.03em;
  line-height: 1;
  margin-bottom: 10px;
}

.sc__sub {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--c-muted);
  font-weight: 500;
}

.sc__sub-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--c-accent);
  flex-shrink: 0;
}

/* Animated bottom bar */
.sc__bar-track {
  height: 3px;
  background: var(--c-border);
  margin: 0 -24px;
}

.sc__bar-fill {
  height: 100%;
  width: 0%;
  background: var(--c-accent);
  border-radius: 0 2px 2px 0;
  animation: bar-grow 1.2s cubic-bezier(0.22, 1, 0.36, 1) forwards;
  animation-delay: 0.2s;
}

@keyframes bar-grow {
  from { width: 0%; }
  to   { width: 68%; }
}
</style>
