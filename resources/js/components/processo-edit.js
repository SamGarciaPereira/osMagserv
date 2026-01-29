document.addEventListener('DOMContentLoaded', function() {
    const formProcesso = document.getElementById('form-processo');
    const statusSelect = document.getElementById('status');
    const btnOpenModal = document.getElementById('btn-open-modal');
    const modal = document.getElementById('faturamentoModal');
    const btnConfirm = document.getElementById('btn-confirm-modal');
    const containerParcelas = document.getElementById('container-parcelas');
    const btnAddParcela = document.getElementById('btn-add-parcela');
    const spanTotal = document.getElementById('total-parcelas');
    const msgValidacao = document.getElementById('msg-validacao');
    const resumoParcelas = document.getElementById('resumo-parcelas');

    const valorOrcamento = parseFloat(formProcesso.getAttribute('data-valor-orcamento')) || 0;
    
    let previousStatus = statusSelect.getAttribute('data-original');

    function formatCurrency(value) {
        return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function verificarDuplicidadeNF() {
        const inputsNF = document.querySelectorAll('input[name*="[nf]"]');
        const valoresVistos = {};
        let temDuplicidade = false;

        inputsNF.forEach(input => {
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            input.classList.add('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
            
            const parent = input.closest('td');
            const msgErro = parent.querySelector('.nf-error-msg');
            if (msgErro) msgErro.remove();
        });

        inputsNF.forEach(input => {
            const valor = input.value.trim();
            if (valor !== "") {
                if (valoresVistos[valor]) {
                    temDuplicidade = true;
                    [input, valoresVistos[valor]].forEach(el => {
                        el.classList.remove('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
                        el.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    });
                    if (!input.closest('td').querySelector('.nf-error-msg')) {
                        input.closest('td').insertAdjacentHTML('beforeend', 
                            `<span class="nf-error-msg text-xs text-red-600 font-bold block mt-1">NF Duplicada</span>`
                        );
                    }
                } else {
                    valoresVistos[valor] = input;
                }
            }
        });

        if (btnConfirm) {
            btnConfirm.disabled = temDuplicidade;
            if (temDuplicidade) {
                btnConfirm.classList.add('opacity-50', 'cursor-not-allowed');
                btnConfirm.title = "Corrija as NFs duplicadas antes de confirmar";
            } else {
                btnConfirm.classList.remove('opacity-50', 'cursor-not-allowed');
                btnConfirm.title = "";
            }
        }
    }

    function atualizarTotal() {
        let total = 0;
        let count = 0;

        document.querySelectorAll('.parcela-valor').forEach(input => {
            total += parseFloat(input.value) || 0;
            count++;
        });

        spanTotal.innerText = formatCurrency(total);
        
        if (Math.abs(total - valorOrcamento) < 0.01) {
            msgValidacao.innerHTML = "(Confere)";
            msgValidacao.className = "ml-1 text-xs font-bold text-green-600";
        } else {
            let diff = valorOrcamento - total;
            msgValidacao.innerHTML = `(Diferença: R$ ${formatCurrency(diff)})`;
            msgValidacao.className = "ml-1 text-xs font-bold text-orange-600";
        }

        if(resumoParcelas) {
            resumoParcelas.innerText = `${count} parcela(s). Total: R$ ${formatCurrency(total)}`;
        }
    }

    window.openFaturamentoModal = function() {
        modal.classList.remove('hidden');
        atualizarTotal();
        verificarDuplicidadeNF();
    }

    window.closeFaturamentoModal = function(save = false) {
        if (save && btnConfirm.disabled) return;

        modal.classList.add('hidden');
        if (!save) {
            if (statusSelect.value === 'Faturado' && previousStatus !== 'Faturado') {
                statusSelect.value = previousStatus;
            }
        } else {
            previousStatus = statusSelect.value;
        }
    }

    statusSelect.addEventListener('change', function() {
        if (this.value === 'Faturado') {
            window.openFaturamentoModal();
        } else {
            previousStatus = this.value;
        }
    });

    if(btnOpenModal) btnOpenModal.addEventListener('click', window.openFaturamentoModal);
    if(btnConfirm) btnConfirm.addEventListener('click', () => window.closeFaturamentoModal(true));

    btnAddParcela.addEventListener('click', function() {
        const uniqueIndex = Date.now(); 

        const row = `
            <tr>
                <td class="px-3 py-2">
                    <input type="text" name="parcelas[${uniqueIndex}][nf]" class="input-nf block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="parcelas[${uniqueIndex}][descricao]" required class="block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </td>
                <td class="px-3 py-2">
                    <input type="number" step="0.01" name="parcelas[${uniqueIndex}][valor]" required class="parcela-valor block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </td>
                <td class="px-3 py-2">
                    <input type="date" name="parcelas[${uniqueIndex}][data_vencimento]" class="block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </td>
                <td class="px-3 py-2 text-center">
                    <button type="button" class="text-red-500 hover:text-red-700 btn-remove-parcela">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        containerParcelas.insertAdjacentHTML('beforeend', row);
        atualizarTotal();
    });

    containerParcelas.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-parcela')) {
            e.target.closest('tr').remove();
            atualizarTotal();
            verificarDuplicidadeNF();
        }
    });

    containerParcelas.addEventListener('input', function(e) {
        if (e.target.classList.contains('parcela-valor')) {
            atualizarTotal();
        }
        if (e.target.name && e.target.name.includes('[nf]')) {
            verificarDuplicidadeNF();
        }
    });

    atualizarTotal();
    setTimeout(verificarDuplicidadeNF, 100);
});