//BARCODE CAMERA READER

async function readBar() {
    //Verifica suporte (iPhone)
    if(!("BarcodeDetector" in window))
    {
        //Se não tem suporte ao BarcodeDetector, redireciona para digitação manual (iPhone)
        window.location = 'index.php?view=search-products';
        return;
    }

    //Detector — Definindo CODE128 para etiqueta padrão Ideal Magazine
    const detector = new BarcodeDetector({formats: ["code_128"]});

    //Acessa câmera traseira (environment)
    let stream;
    try
    {
        stream = await navigator.mediaDevices.getUserMedia(
        {
            video: {facingMode: {exact: "environment"}}
        });
    }
    catch(e)
    {
        window.location = 'index.php?view=search-products';
        return;
    }

    //Inserindo imagem da câmera (stream) no elemento video
    const video = document.querySelector("#video");
    video.srcObject = stream;

    //Aguarda vídeo iniciar (Promise)
    try
    {
        await video.play();
    }
    catch(e)
    {
        window.location = 'index.php?view=search-products';
        return;
    }

    //Loop de leitura
    async function scanCode()
    {
        try
        {
            //Faz o detector analisar o frame atual do vídeo da câmera procurando códigos de barras
            //rawValue: "7891234567890" | format: "ean_13"
            const barcodes = await detector.detect(video);
            if(barcodes.length > 0)
            {
                const barcodeRead = barcodes[0].rawValue;

                let codeProduct = document.createElement('input');
                    codeProduct.setAttribute('name', 'nompr');
                    codeProduct.setAttribute('value', barcodeRead);
                    codeProduct.setAttribute('data-file', 'add-ajax-products');
                    codeProduct.setAttribute('data-base', 'produ01');

                //Para gravar produtos em localStorage
                let productsList = {venda1: []};

                //Evitando substituir itens já salvos em localStorage
                let storedProducts = localStorage.getItem('productsList');
                if(storedProducts) //Se há itens em localStorage adiciona-os a ${productsList}
                {
                    storedProducts = JSON.parse(storedProducts); //Converte para JSON
                    storedProducts = storedProducts.venda1;      //Seleciona venda pelo nome

                    //Cada item encontrado na venda é adicionado à lista
                    Object.entries(storedProducts).forEach((product) => 
                    {
                        productsList.venda1.push(product[1]);
                    });
                }
                    
                //Adicionando produtos ao carrinho de compras
                window.searchAjax(codeProduct).then((responseData) =>
                {   
                    //Só adiciona se produto foi encontrado
                    if(Object.keys(responseData).length > 0)
                    {
                        //Adicionando produtos à lista de produtos
                        productsList.venda1.push(responseData);
                        
                        //Adiciona lista (de produtos) atualizada a localStorage
                        localStorage.setItem('productsList', JSON.stringify(productsList));
                        window.countItems();  
                    }                  
                });

                //Fechando câmera após leitura válida
                if(stream) stream.getTracks().forEach(track => track.stop());                
                if(video)  video.srcObject = null;

                //Redireciona para o carrinho de vendas
                window.location = 'index.php?view=orders';
                return;
            }
        }
        catch(e)
        {
            //Se leitura falhar, redireciona para digitação manual
            window.location = 'index.php?view=search-products';
            return;
        }
        //Ativa loop contínuo de leitura da câmera.
        requestAnimationFrame(scanCode);
    }
    scanCode();
};

//Chamando função, se estiver na página de leitura do código de barras
const barcodePageID = document.querySelector('#barcode');
if(barcodePageID) readBar();