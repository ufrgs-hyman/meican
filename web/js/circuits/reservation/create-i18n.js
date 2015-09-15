var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['and'] = 'e';
dict['pt-BR']['any'] = 'qualquer';
dict['pt-BR']['Network'] = 'Rede';
dict['pt-BR']['Device'] = 'Dispositivo';
dict['pt-BR']['From here'] = 'A partir daqui';
dict['pt-BR']['To here'] = 'Até aqui';
dict['pt-BR']['Add waypoint'] = 'Adicionar intermediário';
dict['pt-BR']['Waypoint'] = 'Intermediário';
dict['pt-BR']['Intra-domain'] = 'Intradomínio';
dict['pt-BR']['Remove endpoint'] = 'Remover ponto final';
dict['pt-BR']['Remove waypoint'] = 'Remover intermediário';
dict['pt-BR']['Remove intra-domain circuit'] = 'Remover circuito intradomínio';
dict['pt-BR']['Error proccessing your request. Contact your administrator.'] = 'Erro ao processar sua requisição. Contate o administrador do sistema.';
dict['pt-BR']['Source end point is undefined or incomplete.'] = 'Ponto final de origem está incompleto.';
dict['pt-BR']['Destination end point is undefined or incomplete.'] = 'Ponto final de destino está incompleto.';
dict['pt-BR']['Yes'] = 'Sim';
dict['pt-BR']['No'] = 'Não';
dict['pt-BR']['Save'] = 'Salvar';
dict['pt-BR']['Cancel'] = 'Cancelar';
dict['pt-BR']['Waypoint device information is required.'] = 'É obrigatório informar o dispositivo para cada rede intermediária.';
dict['pt-BR']['The finish date must be after start date.'] = 'A data de início não pode ser uma data após a data final.';
dict['pt-BR']['The start time or the finish time are invalid.'] = 'Data final ou inicial são inválidas.';
dict['pt-BR']['The bandwidth must be between 1 and 1000.'] = 'A largura de banda deve estar dentro do intervalo de 1 a 1000 Mbps.';
dict['pt-BR']['Error(s) found'] = 'Erro(s) encontrado(s)';
dict['pt-BR']['select'] = 'selecione';
dict['pt-BR']['loading'] = 'aguarde';
dict['pt-BR']['no name'] = 'sem nome';
dict['pt-BR']['click to select device'] = 'selecione o dispositivo';
dict['pt-BR']['Current host is not present in known topology'] = 'O domínio atual não está presente na topologia conhecida.';
dict['pt-BR']['You are not allowed to create a reservation involving these selected domains.'] = 'Você não tem permissão para solicitar reservas envolvendo os domínios selecionados.';

//datepicker
dict['pt-BR'][['January','February','March','April','May','June','July','August','September','October','November','December']] = ['Janeiro',
    'Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
dict['pt-BR'][['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']] = ['Jan','Fev','Mar','Abr','Mai','Jun',
    'Jul','Ago','Set','Out','Nov','Dez'];
dict['pt-BR'][['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']] = ['Domingo','Segunda-feira','Terça-feira',
    'Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
dict['pt-BR'][['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']] = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
dict['pt-BR'][['Su','Mo','Tu','We','Th','Fr','Sa']] = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
dict['pt-BR']['Next'] = 'Próximo';
dict['pt-BR']['Previous'] = 'Anterior';
//

dict['pt-BR']['Duration'] = 'Duração';
dict['pt-BR']['Repeat every'] = 'Repetir a cada';
dict['pt-BR']['Active from'] = 'Ativo a partir de';
dict['pt-BR']['until'] = 'até';
dict['pt-BR']['at'] = 'às';
dict['pt-BR']['on'] = 'nos seguintes dias:';
dict['pt-BR']['time'] = 'vez';
dict['pt-BR']['times'] = 'vezes';
dict['pt-BR']['hour'] = 'hora';
dict['pt-BR']['hours'] = 'horas';
dict['pt-BR']['minute'] = 'minuto';
dict['pt-BR']['minutes'] = 'minutos';
dict['pt-BR']['day'] = 'dia';
dict['pt-BR']['days'] = 'dias';
dict['pt-BR']['week'] = 'semana';
dict['pt-BR']['weeks'] = 'semanas';
dict['pt-BR']['month'] = 'mês';
dict['pt-BR']['months'] = 'meses';
dict['pt-BR']['Sunday'] = ['Domingo'];
dict['pt-BR'][['Monday']] = ['Segunda'];
dict['pt-BR'][['Tuesday']] = ['Terça'];
dict['pt-BR'][['Wednesday']] = ['Quarta'];
dict['pt-BR'][['Thursday']] = ['Quinta'];
dict['pt-BR'][['Friday']] = ['Sexta'];
dict['pt-BR'][['Saturday']] = ['Sábado'];

dict['pt-BR']['A reservation name is required.'] = ['O nome da reserva é obrigatório.'];
dict['pt-BR']['Waypoint information is required.'] = ['Informações de intermediário incompletas.'];
dict['pt-BR']['click to fill waypoint'] = ['clique para completar informações'];

dict['pt-BR']['error'] = 'erro';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR' && dict['pt-BR'][obj]) return dict['pt-BR'][obj];
	else return obj;
}