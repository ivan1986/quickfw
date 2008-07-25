<?php
//------------------------------------------------------------------------------
//*** Português do Brasil	pb - (ptbr)             
//*** Brazilian Portuguese	pb - (ptbr)
// Por Julio Formiga (form1ga@yahoo.com.br)
//------------------------------------------------------------------------------
function setLanguage(){ 
    $lang['='] = "=";  // "igual"; 
    $lang['>'] = ">";  // "maior"; 
    $lang['<'] = "<";  // "menor";            
    $lang['add'] = "Adicionar"; 
    $lang['add_new'] = "+ Adicionar novo"; 
    $lang['add_new_record'] = "Adicionar novo registro";
    $lang['add_new_record_blocked'] = "Security check: attempt of adding a new record! Check your settings, the operation is not allowed!";    
    $lang['adding_operation_completed'] = "Adição realizada com sucesso!";
    $lang['adding_operation_uncompleted'] = "A adição não foi finalizada!";                                    
    $lang['and'] = "e";
    $lang['any'] = "any";                                                 
    $lang['ascending'] = "Crescente"; 
    $lang['back'] = "Voltar"; 
    $lang['cancel'] = "Cancelar";
    $lang['cancel_creating_new_record'] = "Tem certeza que deseja cancelar a adição de um novo registro?";
    $lang['check_all'] = "Marcar todos";
    $lang['clear'] = "Clear";    
    $lang['create'] = "Criar"; 
    $lang['create_new_record'] = "Criar um novo registro"; 
    $lang['current'] = "atual"; 
    $lang['delete'] = "Apagar"; 
    $lang['delete_record'] = "Apagar registro";
    $lang['delete_record_blocked'] = "Security check: attempt of deleting a record! Check your settings, the operation is not allowed!";    
    $lang['delete_selected'] = "Apagar selecionados";
    $lang['delete_selected_records'] = "Você tem certeza que deseja apagar os registros selecionados?";
    $lang['delete_this_record'] = "Você tem certeza que deseja apagar este registro?";                                 
    $lang['deleting_operation_completed'] = "Registro(s) apagado(s) com secesso!";
    $lang['deleting_operation_uncompleted'] = "Registro(s) não foi(foram) apagados!";                                    
    $lang['descending'] = "Decrescente";
    $lang['details'] = "Detalhes";
    $lang['details_selected'] = "Ver selecionados";            
    $lang['edit'] = "Editar";
    $lang['edit_selected'] = "Editar selecionados";
    $lang['edit_record'] = "Editar registro"; 
    $lang['edit_selected_records'] = "Você tem certeza que deseja editar os registros selecionados?";               
    $lang['errors'] = "Erros";       
    $lang['export_to_excel'] = "Exportar para o Excel";
    $lang['export_to_xml'] = "Exportar para o XML";
    $lang['export_cvs_message'] = "Deseja exportar o resultado em um arquivo .cvs?";
    $lang['export_message'] = "<label class=\"class_label\">O arquivo _FILE_ está pronto. Depois que terminar de baixar,</label> <a class=\"class_error_message\" href=\"javascript: window.close();\">feche esta janela</a>.";
    $lang['field'] = "Campo"; 
    $lang['field_value'] = "Valor do campo";
    $lang['file_find_error'] = "Não foi possível achar o arquivo: <b>_FILE_</b>. <br>Verifique se este arquivo existe e se você informou o caminho corretamente!";                                    
    $lang['file_opening_error'] = "Não foi possível abrir o arquivo. Verifique suas permissões.";                        
    $lang['file_writing_error'] = "Não foi possível escrever no arquivo. Verifique as permissões de escrita!";
    $lang['file_invalid file_size'] = "Tamanho do arquivo inválido";
    $lang['file_uploading_error'] = "Foi encontrado um erro ao tentar enviar o arquivo, por favor tente novamente!";
    $lang['file_deleting_error'] = "Foi encontrado um erro ao tentar apagar o arquivo!";
    $lang['first'] = "primeiro";
    $lang['handle_selected_records'] = "Tem certeza de que deseja processar os registros selecionados?";
    $lang['hide_search'] = "Esconder Procura";
    $lang['last'] = "último";
    $lang['like'] = "like";
    $lang['like%'] = "like%";  // "begins with";
    $lang['%like'] = "%like";  // "ends with";
    $lang['%like%'] = "%like%";  
    $lang['loading_data'] = "lendo dados...";
    $lang['max'] = "max";
    $lang['next'] = "próximo";
    $lang['no'] = "Não";
    $lang['no_data_found'] = "Não foram encontrados dados";
    $lang['no_data_found_error'] = "Não foram encontrados dados! Por favor verifique cuidadosamente a sintaxe do seu código!<br>Ele por diferenciar maiúsculas e minúsculas ou pode ter símbolos não identificados.";
    $lang['no_image'] = "Sem imagem";
    $lang['not_like'] = "not like";
    $lang['of'] = "de";
    $lang['or'] = "ou";
    $lang['pages'] = "Páginas";
    $lang['page_size'] = "Nº de resultados por página"; 
    $lang['previous'] = "anterior";
    $lang['printable_view'] = "Visualizar impressão";
    $lang['print_now'] = "Imprimir";
    $lang['print_now_title'] = "Clique aqui para imprimir esta página";
    $lang['record_n'] = "Registro #";
    $lang['refresh_page'] = "Refresh Page";    
    $lang['remove'] = "Remover";
    $lang['reset'] = "Reiniciar";
    $lang['results'] = "Resultados";
    $lang['required_fields_msg'] = "<font color='#cd0000'>*</font> Itens marcados com um asterisco são obrigatórios";
    $lang['search'] = "Busca";
    $lang['search_d'] = "Busca"; // (description) 
    $lang['search_type'] = "Tipo de busca"; 
    $lang['select'] = "selecionar";
    $lang['set_date'] = "Definir data";
    $lang['sort'] = "Sort";        
    $lang['total'] = "Total";
    $lang['turn_on_debug_mode'] = "Para mais informações, mude para o modo 'debug'.";
    $lang['uncheck_all'] = "Desmarcar todos";
    $lang['unhide_search'] = "Mostrar a Busca";
    $lang['unique_field_error'] = "O campo _FIELD_ permite apenas valores únicos - favor informar novamente!";            
    $lang['update'] = "Atualizar";
    $lang['update_record'] = "Atualizar registro";
    $lang['update_record_blocked'] = "Security check: attempt of updating a record! Check your settings, the operation is not allowed!";    
    $lang['updating_operation_completed'] = "A atualização foi realizada com sucesso!";
    $lang['updating_operation_uncompleted'] = "A atualização está incompleta!";                        
    $lang['upload'] = "Enviar arquivo";
    $lang['view'] = "Visualizar";
    $lang['view_details'] = "Ver detalhes";
    $lang['warnings'] = "Alertas";
    $lang['with_selected'] = "Com selecionados";
    $lang['wrong_field_name'] = "Nome de campo errado";
    $lang['wrong_parameter_error'] = "Parâmetro errado em [<b>_FIELD_</b>]: _VALUE_";
    $lang['yes'] = "Sim";
    
    return $lang;
}
?>