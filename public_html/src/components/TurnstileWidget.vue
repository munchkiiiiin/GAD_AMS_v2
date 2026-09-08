<template>
  <div class="turnstile-container mb-4">
    <div ref="turnstileElement"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const emit = defineEmits(['verify', 'error', 'expired']);
const turnstileElement = ref(null);
let widgetId = null;

const siteKey = import.meta.env.VITE_TURNSTILE_SITE_KEY || '0x4AAAAAAEmDKBaBSi1GMsqq';

let turnstileScriptPromise;

const ensureTurnstileScript = () => {
  if (typeof window === 'undefined') {
    return Promise.reject(new Error('Turnstile requires a browser environment.'));
  }

  if (window.turnstile) {
    return Promise.resolve();
  }

  if (turnstileScriptPromise) {
    return turnstileScriptPromise;
  }

  turnstileScriptPromise = new Promise((resolve, reject) => {
    const existingScript = document.querySelector('script[data-turnstile="true"]');

    if (existingScript) {
      existingScript.addEventListener('load', () => resolve(), { once: true });
      existingScript.addEventListener('error', () => reject(new Error('Failed to load Turnstile script.')), { once: true });
      return;
    }

    const script = document.createElement('script');
    script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
    script.async = true;
    script.defer = true;
    script.setAttribute('data-turnstile', 'true');
    script.onload = () => resolve();
    script.onerror = () => reject(new Error('Failed to load Turnstile script.'));
    document.head.appendChild(script);
  });

  return turnstileScriptPromise;
};

onMounted(() => {
  const renderWidget = async () => {
    try {
      await ensureTurnstileScript();

      if (window.turnstile && turnstileElement.value && siteKey) {
        widgetId = window.turnstile.render(turnstileElement.value, {
          sitekey: siteKey,
          callback: (token) => {
            emit('verify', token);
          },
          'error-callback': () => {
            emit('error');
          },
          'expired-callback': () => {
            emit('expired');
          }
        });
      }
    } catch (error) {
      emit('error');
    }
  };

  renderWidget();
});

onUnmounted(() => {
  if (widgetId !== null && window.turnstile) {
    window.turnstile.remove(widgetId);
  }
});

const reset = () => {
  if (widgetId !== null && window.turnstile) {
    window.turnstile.reset(widgetId);
  }
};

defineExpose({ reset });
</script>

<style scoped>
.turnstile-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 65px;
}
</style>
