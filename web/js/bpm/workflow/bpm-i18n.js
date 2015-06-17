var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Workflow Name:'] = 'Nome do Workflow:';
dict['pt-BR']['Enter a name'] = 'Insira um nome';
dict['pt-BR']['(click to edit)'] = '(Clique para editar)';
dict['pt-BR']['cancel'] = 'cancelar';
dict['pt-BR']['Cancel'] = 'Cancelar';
dict['pt-BR']['Drag and drop these elements'] = 'Arraste e largue estes elementos';
dict['pt-BR']['This Workflow is enabled for domain '] = 'Este workflow está ativado para o domínio ';
dict['pt-BR']['. This domain will not have an enabled workflow. Do you confirm?'] = '. Este domínio não vai ter um workflow ativo. Deseja continuar?';
dict['pt-BR']['Delete this Workflow?'] = 'Deletar este workflow?';
dict['pt-BR']['Only disabled Workflows can be edited.'] = 'Apenas workflows desativados podem ser editados.';
dict['pt-BR']['Yes'] = 'Sim';
dict['pt-BR']['No'] = 'Não';
dict['pt-BR']['Please, choose a name.'] = 'Por favor, insira um nome.';
dict['pt-BR']['to'] = 'até';
dict['pt-BR']['source'] = 'origem';
dict['pt-BR']['destination'] = 'destino';
dict['pt-BR']['previous'] = 'anterior';
dict['pt-BR']['next'] = 'próximo';
dict['pt-BR']['Sunday'] = 'Domingo';
dict['pt-BR']['Monday'] = 'Segunda-feira';
dict['pt-BR']['Tuesday'] = 'Terça-feira';
dict['pt-BR']['Wednesday'] = 'Quarta-feira';
dict['pt-BR']['Thursday'] = 'Quinta-feira';
dict['pt-BR']['Friday'] = 'Sexta-feira';
dict['pt-BR']['Saturday'] = 'Sábado';
dict['pt-BR']['minutes'] = 'minutos';
dict['pt-BR']['hours'] = 'horas';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR') return dict['pt-BR'][obj];
	else return obj;
}