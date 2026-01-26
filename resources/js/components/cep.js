document.addEventListener('DOMContentLoaded', function () {
    const btnBuscar = document.getElementById('btn-buscar-cep');
    const cepInput = document.getElementById('cep');

    if (btnBuscar && cepInput) {
        btnBuscar.addEventListener('click', function (e) {
            e.preventDefault();
            
            let cep = cepInput.value.replace(/\D/g, '');

            if (cep.length === 8) {
                const originalContent = btnBuscar.innerHTML;
                btnBuscar.innerHTML = '<i class="bi bi-arrow-repeat inline-block animate-spin text-lg"></i>';
                btnBuscar.disabled = true;
                cepInput.disabled = true;

                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            const logradouro = document.getElementById('logradouro');
                            const bairro = document.getElementById('bairro');
                            const cidade = document.getElementById('cidade');
                            const estado = document.getElementById('estado');
                            const numero = document.getElementById('numero');

                            if(logradouro) logradouro.value = data.logradouro;
                            if(bairro) bairro.value = data.bairro;
                            if(cidade) cidade.value = data.localidade;
                            if(estado) estado.value = data.uf;

                            if(numero) numero.focus();
                        } else {
                            alert('CEP não encontrado.');
                            cepInput.focus();
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao buscar o CEP.');
                    })
                    .finally(() => {
                        btnBuscar.innerHTML = originalContent;
                        btnBuscar.disabled = false;
                        cepInput.disabled = false;
                    });
            } else {
                alert('Por favor, digite um CEP válido com 8 números.');
                cepInput.focus();
            }
        });
    }
});