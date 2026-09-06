import { ref, onMounted, onUnmounted } from 'vue';

const canInstall = ref(typeof window !== 'undefined' ? !!window.deferredPwaPrompt : false);

export function usePwaInstall() {
  const updateInstallState = () => {
    canInstall.value = typeof window !== 'undefined' && !!window.deferredPwaPrompt;
  };

  const installApp = async () => {
    if (typeof window === 'undefined' || !window.deferredPwaPrompt) return false;
    const promptEvent = window.deferredPwaPrompt;
    promptEvent.prompt();
    const { outcome } = await promptEvent.userChoice;
    if (outcome === 'accepted') {
      window.deferredPwaPrompt = null;
      canInstall.value = false;
      return true;
    }
    return false;
  };

  onMounted(() => {
    updateInstallState();
    if (typeof window !== 'undefined') {
      window.addEventListener('pwa-prompt-available', updateInstallState);
      window.addEventListener('appinstalled', () => {
        window.deferredPwaPrompt = null;
        canInstall.value = false;
      });
    }
  });

  onUnmounted(() => {
    if (typeof window !== 'undefined') {
      window.removeEventListener('pwa-prompt-available', updateInstallState);
    }
  });

  return {
    canInstall,
    installApp
  };
}
