//CPF Mask
function maskCPF(cpfValue)
{
    //Remove tudo que não for número
    cpfValue = cpfValue.replace(/\D/g, '');

    //Limita a 11 dígitos
    if(cpfValue.length > 11) cpfValue = cpfValue.slice(0, 11);

    //Aplica a máscara: 000.000.000-00
    cpfValue = cpfValue.replace(/(\d{3})(\d)/, '$1.$2');
    cpfValue = cpfValue.replace(/(\d{3})(\d)/, '$1.$2');
    cpfValue = cpfValue.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

    return cpfValue;
}

const cpfField = document.getElementById('cpfus'); //Seleciona o campo
if(cpfField)
{
    cpfField.value = maskCPF(cpfField.value); //Formata o valor já existente
    cpfField.addEventListener('input', () => //Aplicar máscara enquanto digita
    {
        cpfField.value = maskCPF(cpfField.value);
    });
}

//CNPJ Mask
function maskCNPJ(cnpjValue)
{
    //Remove tudo que não for número
    cnpjValue = cnpjValue.replace(/\D/g, '');

    //Limita a 14 dígitos
    if(cnpjValue.length > 14) cnpjValue = cnpjValue.slice(0, 14);

    if(cnpjValue.length <= 11)
    {
        //Aplica a máscara: 000.000.000-00
        cnpjValue = cnpjValue.replace(/(\d{3})(\d)/, '$1.$2');
        cnpjValue = cnpjValue.replace(/(\d{3})(\d)/, '$1.$2');
        cnpjValue = cnpjValue.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    else if(cnpjValue.length > 11)
    {
        //Aplica a máscara: 00.000.000/0000-00
        cnpjValue = cnpjValue.replace(/^(\d{2})(\d)/, '$1.$2');
        cnpjValue = cnpjValue.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        cnpjValue = cnpjValue.replace(/\.(\d{3})(\d)/, '.$1/$2');
        cnpjValue = cnpjValue.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }

    return cnpjValue;
}

const cnpjField = document.getElementById('docfo'); //Seleciona o campo
if(cnpjField)
{
    cnpjField.value = maskCNPJ(cnpjField.value); //Formata o valor já existente
    cnpjField.addEventListener('input', () =>
    {
        cnpjField.value = maskCNPJ(cnpjField.value);
    });
}

//PHONE Mask
function phoneMask(phoneValue)
{
    //Remove tudo que não for número
    phoneValue = phoneValue.replace(/\D/g, '');

    //Limita a 11 dígitos
    if(phoneValue.length > 11) phoneValue = phoneValue.slice(0, 11);

    //Aplica a máscara conforme o tamanho
    if(phoneValue.length == 10)
    {
        phoneValue = phoneValue.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    }
    else if(phoneValue.length == 11)
    {
        phoneValue = phoneValue.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    }
    else
    {
        phoneValue = phoneValue.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2$3');
    }

    return phoneValue;
}

const phoneField = document.getElementById('fonus') || document.getElementById('fonfo'); //Seleciona o campo
if(phoneField)
{
    phoneField.value = phoneMask(phoneField.value); //Formata o valor já existente
    phoneField.addEventListener('input', () => //Aplicar máscara enquanto digita
    {
        phoneField.value = phoneMask(phoneField.value);
    });
}