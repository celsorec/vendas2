//CHANGE ACCESS PDF - Botão ao clique alterna entre campo para upload de novo PDF (substituição) e link para acesso ao PDF
const fieldFiles   = document.querySelector("#files");          //
const accessPDF    = document.querySelector("#access-pdf");     //
const btnChangePDF = document.querySelector("#btn-change-pdf"); //Botão alternação ao clique

if(btnChangePDF)
{
    btnChangePDF.addEventListener('click', () => 
    {
        accessPDF.classList.toggle('hidden');
        fieldFiles.parentElement.classList.toggle('hidden');
        
        let textBTN = btnChangePDF.querySelector('.text').innerText;
        if(textBTN != 'Manter PDF')
        {
            textBTN = 'Manter PDF';
            btnChangePDF.classList.add('newtext');
        }
        else
        {
            textBTN = 'Substituir PDF';
            btnChangePDF.classList.remove('newtext');
        }
        btnChangePDF.querySelector('.text').innerText = textBTN;
    });
}