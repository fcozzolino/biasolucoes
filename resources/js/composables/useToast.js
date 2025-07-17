// resources/js/composables/useToast.js
export function useToast() {
  const createToast = (message, type = 'success') => {
    // Cria container se não existir
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.id = 'toast-container';
      toastContainer.className = 'position-fixed top-0 end-0 p-3';
      toastContainer.style.zIndex = '9999';
      document.body.appendChild(toastContainer);
    }

    // Define classes baseadas no tipo
    const bgClass = {
      success: 'bg-success',
      error: 'bg-danger',
      warning: 'bg-warning',
      info: 'bg-info'
    }[type] || 'bg-success';

    // Cria o toast
    const toastId = `toast-${Date.now()}`;
    const toastHtml = `
      <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            ${message}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    `;

    // Adiciona ao container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

    // Inicializa e mostra o toast
    const toastElement = document.getElementById(toastId);
    if (toastElement && window.bootstrap) {
      const toast = new window.bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
      });

      toast.show();

      // Remove o elemento após ser escondido
      toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
      });
    } else {
      // Fallback se Bootstrap não estiver disponível
      console.log(`${type.toUpperCase()}: ${message}`);

      // Remove após 3 segundos
      setTimeout(() => {
        const el = document.getElementById(toastId);
        if (el) el.remove();
      }, 3000);
    }
  };

  const showSuccessToast = (message) => {
    createToast(message, 'success');
  };

  const showErrorToast = (message) => {
    createToast(message, 'error');
  };

  const showWarningToast = (message) => {
    createToast(message, 'warning');
  };

  const showInfoToast = (message) => {
    createToast(message, 'info');
  };

  return {
    showSuccessToast,
    showErrorToast,
    showWarningToast,
    showInfoToast
  };
}
