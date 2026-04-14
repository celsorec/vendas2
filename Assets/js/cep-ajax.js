//AJAX SEARCH
//AUTO COMPLETE ADDRESS BY CEP
const inputCEP = document.querySelector('#cepfo');
if(inputCEP)
{
    let timer;
    inputCEP.addEventListener('input', () =>
    {
        clearTimeout(timer);
        timer = setTimeout(() => {
            if(inputCEP.value.length === 8)
            {
                fetch('https://viacep.com.br/ws/'+inputCEP.value+'/json/')
                .then(response => response.json())
                .then(address =>
                {
                    document.getElementById('estfo').value = address.uf || '';
                    document.getElementById('cidfo').value = address.localidade || '';
                    document.getElementById('logfo').value = address.logradouro || '';
                    document.getElementById('baifo').value = address.bairro || '';
                })
                .catch(error => console.error('Erro:', error));
            }
        }, 500);
    });
}