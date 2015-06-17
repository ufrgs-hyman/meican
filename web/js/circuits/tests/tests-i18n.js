var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Disable auto refresh'] = 'Desativar auto-atualização';
dict['pt-BR']['Enable auto refresh'] = 'Ativar auto-atualização';
dict['pt-BR']['loading'] = 'aguarde';
dict['pt-BR']['select'] = 'selecione';
dict['pt-BR']['Hour'] = 'Horário';
dict['pt-BR']['Weekday'] = 'Dias da semana';
dict['pt-BR']['Month'] = 'Mês';
dict['pt-BR']['Save'] = "Salvar";
dict['pt-BR']['Cancel'] = 'Cancelar';
dict['pt-BR']['no name'] = 'sem nome';
dict['pt-BR']['New'] = 'Novo';
dict['pt-BR']['Never'] = 'Nunca';
dict['pt-BR']['None'] = 'Sem resultado';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR') return dict['pt-BR'][obj];
	else return obj;
}