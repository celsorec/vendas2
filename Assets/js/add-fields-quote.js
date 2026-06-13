//ADD FIELDS QUOTE
//Selecionando itens para anexar campos dinâmicos
const formRequest = document.querySelector('#form_request .columns');
const lastChild   = document.querySelector('#form_request h3');
const dynamicDiv  = document.querySelector('#form_request #request-quote') || document.querySelector('#form_request #request-order');

if(dynamicDiv) //Se formulário de solicitação estiver presente
{
    //BTN adicionar cotação
    const btnAdd     = document.createElement('button');
    btnAdd.className = 'btn small-btn';
    btnAdd.innerText = 'Adicionar Cotação';
    btnAdd.type      = 'button';

    //Container para BTN
    const wrapperBtn = document.createElement('div');
    wrapperBtn.className = 'col-12';
    wrapperBtn.appendChild(btnAdd);

    //Anexando container BTN ao container principal
    dynamicDiv.appendChild(wrapperBtn);

    //Informações para gerar campos e labels de diferentes formatos: input, select...
    const fieldConfig =
    [        
        {proce: ['Processo', 'codpr']},
        {forne: ['Fornecedor', 'codfo']},
        {valor: ['Preço', 'preco']},
        {obser: ['Observação', 'obsct']},
        {sqlid: ['ID', 'sql_rowid']}
    ];

    //Função cria campos artificiais e anexa à página form_request.php
    let countId = 0;
    function addFields(codpr=null, codfo=null, preco=null, obsct=null, sql_rowid=null,) //Values de cotationValues
    {
        //Para agrupar campos + label e botão remover
        let wrapperFields = document.createElement('div');
        wrapperFields.className = 'wrapper-fields';

        fieldConfig.forEach((element) =>
        {
            //Grupo para envolver cada label + input ou label + select
            let group = document.createElement('div');

            //Se campo é proce -> CÓDIGO DO PROCESSO
            if(element['proce'])
            {
                //Grupo deste campo -> Input.proce
                group.className = 'fields-group hidden';

                //Label do campo com número processo
                let label       = document.createElement('label');
                label.htmlFor   = element['proce'][1]+'-'+countId;
                label.innerText = element['proce'][0];
                
                //Input do campo com número processo
                let proce  = document.createElement('input');
                proce.id   = element['proce'][1]+'-'+countId;
                proce.name = 'cotac['+countId+']['+element['proce'][1]+']';
                proce.type = 'text';

                //Preenchimento de campos com valores vindos do banco de dados
                if(codpr != null) proce.value = codpr;
                
                //Anexando label + input ao grupo
                group.appendChild(label);
                group.appendChild(proce);
            }

            //Se campo é forne -> CÓDIGO - NOME FORNECEDOR
            if(element['forne'])
            {
                //Grupo deste campo -> Input.forne
                group.className = 'fields-group col-6 search';
                
                //Adicionando 'display ajax' para mostrar resultado das buscas
                let displayAjax = document.createElement('div');
                displayAjax.classList.add('display-ajax');
                displayAjax.style.marginTop = '-5px'; //-5 para alinhar ícone da lixeira (gap 5px)
                group.appendChild(displayAjax);

                //Label do campo fornecedor
                let label       = document.createElement('label');
                label.htmlFor   = element['forne'][1]+'-'+countId;
                label.innerText = element['forne'][0];

                //Input do campo fornecedor
                let forne       = document.createElement('input');
                forne.id        = element['forne'][1]+'-'+countId;
                forne.name      = 'cotac['+countId+']['+element['forne'][1]+']';
                forne.type      = 'text';
                forne.className = 'search_field';
                forne.setAttribute('required', '');
                forne.setAttribute('pattern', '^\\d+\\s*-\\s*.+$');
                
                //Atributos de configuração das buscas Ajax
                forne.setAttribute('data-class', 'search_field');
                forne.setAttribute('data-table', 'forne');
                forne.setAttribute('data-likes', 'codfo, fanfo');
                forne.setAttribute('data-columns', 'codfo, fanfo, sql_rowid');
                
                //Limpando campo ao clicar no campo
                forne.addEventListener('click', () => forne.value = null);

                //Preenchimento de campos com valores vindos do banco de dados
                if(codfo != null) forne.value = codfo;

                //Anexando label + input ao grupo
                group.insertBefore(label, displayAjax);
                group.insertBefore(forne, displayAjax);
            }

            //Se campo é valor -> PRECO COTADO COM FORNECEDOR
            if(element['valor'])
            {
                //Grupo deste campo -> Input.valor
                group.className = 'fields-group col-3';
                
                //Label de quantidade de itens
                let label       = document.createElement('label');
                label.htmlFor   = element['valor'][1]+'-'+countId;
                label.innerText = element['valor'][0];
                
                //Input de quantidade de itens
                let valor  = document.createElement('input');
                valor.id   = element['valor'][1]+'-'+countId;
                valor.name = 'cotac['+countId+']['+element['valor'][1]+']';
                valor.type = 'number';
                valor.setAttribute('required', '');

                //Preenchimento de campos com valores vindos do banco de dados
                if(preco != null) valor.value = preco;
                
                //Anexando label + input ao grupo
                group.appendChild(label);
                group.appendChild(valor);
            }

            //Se campo é obser -> OBSERVAÇÃO
            if(element['obser'])
            {
                //Grupo deste campo -> Input.obser
                group.className = 'fields-group col-6';
                
                //Label do campo de observações
                let label       = document.createElement('label');
                label.htmlFor   = element['obser'][1]+'-'+countId;
                label.innerText = element['obser'][0];
                
                //Input do campo de observações
                let obser  = document.createElement('input');
                obser.id   = element['obser'][1]+'-'+countId;
                obser.name = 'cotac['+countId+']['+element['obser'][1]+']';
                obser.type = 'text';

                //Preenchimento de campos com valores vindos do banco de dados
                if(obsct != null) obser.value = obsct;
                
                //Anexando label + input ao grupo
                group.appendChild(label);
                group.appendChild(obser);
            }

            //Se campo é sqlid -> SQL_ROWID
            if(element['sqlid'])
            {
                //Grupo deste campo -> Input.sqlid
                group.className = 'fields-group hidden';
                
                //Label do campo do ID
                let label       = document.createElement('label');
                label.htmlFor   = element['sqlid'][1]+'-'+countId;
                label.innerText = element['sqlid'][0];
                
                //Input do campo do ID
                let sqlid  = document.createElement('input');
                sqlid.id   = element['sqlid'][1]+'-'+countId;
                sqlid.name = 'cotac['+countId+']['+element['sqlid'][1]+']';
                sqlid.type = 'text';

                //Preenchimento de campos com valores vindos do banco de dados
                if(sql_rowid != null) sqlid.value = sql_rowid;
                
                //Anexando label + input ao grupo
                group.appendChild(label);
                group.appendChild(sqlid);
            }

            //Anexando grupos à div de cotação
            wrapperFields.appendChild(group);
        });

        //Grupo para envolver BTN remover cotação
        let groupBtn = document.createElement('div');
        groupBtn.className = 'fields-group col-1 btn-remove-wrapper';

        //BTN remover cotação
        let btnRemove = document.createElement('button');
        btnRemove.className = 'btn small-btn';
        btnRemove.type = 'button';
        btnRemove.addEventListener('click', () => {btnRemove.parentElement.parentElement.remove()});
        groupBtn.appendChild(btnRemove);

        //Anexando grupos ao container principal
        wrapperFields.appendChild(groupBtn);
        dynamicDiv.insertBefore(wrapperFields, btnAdd.parentElement);
        countId++;
    }

    //Aciona função para criar campos artificiais
    btnAdd.addEventListener('click', (e) =>
    {
        e.preventDefault();
        addFields();
    });

    //Aciona função e preenche valores dos campos artificias com informações vindas do banco de dados; formulário em modo de edição
    let cotationValues = localStorage.getItem('cotationValues');
    cotationValues = JSON.parse(cotationValues);

    //Chamando função addFields para cada conjunto de valores vindos do banco de dados em JSON para preencher campos do formulário
    if(cotationValues)
    {
        cotationValues.forEach((item) => 
        {
            addFields(item.codpr, item.codfo, item.preco, item.obsct, item.sql_rowid);
        });
    }
}