
window.openAnexoModal = function (id, nome = '') {
    console.log('Tentando abrir modal para ID:', id);

    const modal = document.getElementById("anexoModal");
    const form = modal ? modal.querySelector('form') : null;

    if (modal) {
        if (form) form.reset();

        const idInput = document.getElementById("modalModelId");
        const nameLabel = document.getElementById("modalModelName");

        if (idInput) idInput.value = id;
        if (nameLabel) nameLabel.innerText = nome || ('Registro #' + id);

        modal.classList.remove("hidden");
        
        modal.style.display = ''; 
        
        console.log('Modal aberto com sucesso.');
    } else {
        console.error('ERRO: Modal com id "anexoModal" não encontrado no HTML.');
    }
};

window.closeAnexoModal = function () {
    const modal = document.getElementById("anexoModal");
    if (modal) {
        modal.classList.add("hidden");
        console.log('Modal fechado.');
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById("anexoModal");
    
    if (modal) {
        modal.addEventListener('click', function (e) {       
            if (e.target === modal) {
                closeAnexoModal();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeAnexoModal();
        }
    });
});