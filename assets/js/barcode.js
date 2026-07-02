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

                //Salvar código de barras em localStorage
                localStorage.setItem('barcodeRead', barcodeRead)

                //nompr.value = barcodeRead.substring(0, 8);
                //nompr.dispatchEvent(new Event('input', { bubbles: true })); //Simula digitação manual (input) para acionar o Ajax

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