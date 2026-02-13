document.addEventListener('DOMContentLoaded', function() {
    const tipoContratoSelect = document.getElementById('tipo_contrato'); 
    const sectionDocs = document.getElementById('section-documentos');
    const fieldIntermitente = document.getElementById('field-contrato-intermitente');

    function toggleDocs() {
        const valor = tipoContratoSelect.value;
        
        if (valor === 'Fixo' || valor === 'Intermitente') {
            sectionDocs.classList.remove('hidden');
            
            if (valor === 'Intermitente') {
                fieldIntermitente.classList.remove('hidden');
            } else {
                fieldIntermitente.classList.add('hidden');
                document.getElementById('doc_contrato_intermitente').value = ''; 
            }
        } else {
            sectionDocs.classList.add('hidden');
            fieldIntermitente.classList.add('hidden');
        }
    }

    if(tipoContratoSelect) {
        tipoContratoSelect.addEventListener('change', toggleDocs);
        toggleDocs(); 
    }
});