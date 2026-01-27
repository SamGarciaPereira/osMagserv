document.addEventListener('DOMContentLoaded', function() {
    
    const radioCliente = document.getElementById('radio_cliente');
    const radioObra = document.getElementById('radio_obra');
    const containerObra = document.getElementById('endereco_obra_container');
    const inputCep = document.getElementById('cep_obra');

    if (!radioCliente || !radioObra) return;

    function toggleEndereco() {
        if (radioObra.checked) {
            containerObra.classList.remove('hidden');
        } else {
            containerObra.classList.add('hidden');
            limparCamposObra();
        }
    }

    radioCliente.addEventListener('change', toggleEndereco);
    radioObra.addEventListener('change', toggleEndereco);

    if (inputCep) {
        inputCep.addEventListener('blur', function(e) {
            const cep = e.target.value.replace(/\D/g, '');

            if (cep.length === 8) {
                document.getElementById('logradouro_obra').value = 'Buscando...';
                document.getElementById('bairro_obra').value = '...';

                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('logradouro_obra').value = data.logradouro;
                            document.getElementById('bairro_obra').value = data.bairro;
                            document.getElementById('cidade_obra').value = data.localidade;
                            document.getElementById('uf_obra').value = data.uf;
                            
                            document.getElementById('numero_obra').focus();
                        } else {
                            alert('CEP não encontrado.');
                            limparCamposObra();
                        }
                    })
                    .catch(() => {
                        alert('Erro ao buscar CEP.');
                        limparCamposObra();
                    });
            }
        });
    }

    function limparCamposObra() {
        const inputs = containerObra.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
    }
});