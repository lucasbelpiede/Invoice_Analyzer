<?php
    //Conexão com o Banco de Dados
    include('connection.php');
    
    //Validação do Upload do Arquivo
    if(isset($_POST['submitfile'])){
        $uploadedfile = $_FILES['selectfile'];
        $fileextension = explode('.',$uploadedfile['name']);

        //Validação da Extensão do Arquivo
        if($fileextension[sizeof($fileextension)-1] != 'xml'){
            echo('<h3 align="center" style="color: red;">Apenas Notas Fiscais no Formato XML são Aceitas</h3>');
        }
        else{
            $filecontent = simplexml_load_file($uploadedfile['tmp_name']);
            
            //Validação do Tipo de Nota Fiscal
            if(isset($filecontent->NFe)){
                $filevalidation = $filecontent->NFe->infNFe;
            }
            else{
                if(isset($filecontent->infNFe)){
                    $filevalidation = $filecontent->infNFe;   
                }
                else{
                    if(isset($filecontent->NFSe)){
                        $filevalidation = $filecontent->NFSe->infNFSe;
                    }
                    else{
                        if(isset($filecontent->infNFSe)){
                            $filevalidation = $filecontent->infNFSe;
                        }
                        else{
                            echo('<h3 align="center" style="color: red;">Nota Fiscal Inválida</h3>'); 
                        }
                    }
                }
            }

            if(isset($filevalidation)){
                //Validação do CNPJ da Empresa
                if(strlen($filevalidation->emit->CNPJ) == 14){
                    $id = $filevalidation['Id'];
                    $cnpj = $filevalidation->emit->CNPJ;
                    $name = $filevalidation->emit->xNome;
                    $address = $filevalidation->emit->enderNac->saveXML();
                    $phone = $filevalidation->emit->fone;
                    $email = $filevalidation->emit->email;
                    
                    move_uploaded_file($uploadedfile['tmp_name'],__DIR__.'/invoices/'.$uploadedfile['name']);

                    //Armazenamento dos Dados da Nota Fiscal no Banco de Dados
                    $connect->query("INSERT INTO invoicesdata (id, cnpj, name, address, phone, email) VALUES ('$id', '$cnpj', '$name', '$address', '$phone', '$email')");
                    
                    //Exibição dos Dados da Nota Fiscal
                    if(strlen($id) > 0)
                        echo('<h3 align="center" style="color: green;">Número da Nota Fiscal: '.$id.'</h3>');
                    if(strlen($cnpj) > 0)
                        echo('<h3 align="center" style="color: green;">CNPJ da Empresa: '.$cnpj.'</h3>');
                    if(strlen($name) > 0)
                        echo('<h3 align="center" style="color: green;">Razão Social da Empresa: '.$name.'</h3>');
                    if(strlen($address) > 0)
                        echo('<h3 align="center" style="color: green;">Endereço da Empresa: '.$address.'</h3>');
                    if(strlen($phone) > 0)
                        echo('<h3 align="center" style="color: green;">Telefone da Empresa: '.$phone.'</h3>');
                    if(strlen($email) > 0)
                        echo('<h3 align="center" style="color: green;">E-mail da Empresa: '.$email.'</h3>'); 
                }
                else{
                    echo('<h3 align="center" style="color: red;">CNPJ da Empresa Inválido</h3>');
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisador de Nota Fiscal</title>
</head>
<body style="background-color: gray;">
    <!--Tela de Upload do Arquivo-->
    <h1 align="center" style="color: aqua;">Analisador de Nota Fiscal</h1>
    <h2 align="center" style="color: aqua;">Selecione Apenas Notas Fiscais no Formato XML</h2>
    
    <div align="center">
        <form method="post" enctype="multipart/form-data" style="color: aqua;">
            <input type="file" name="selectfile">
            <input type="submit" name="submitfile" value="Enviar">   
        </form>
    </div>
</body>
</html>