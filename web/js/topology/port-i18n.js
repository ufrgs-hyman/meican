var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['The selected ports will be deleted.<br>Do you confirm?'] = 'As portas selecionadas serão removidas.<br>Você deseja continuar?';
dict['pt-BR']['Yes'] = 'Sim';
dict['pt-BR']['No'] = 'Não';
dict['pt-BR']['Delete this port?'] = 'Remover esta porta?';
dict['pt-BR']['Confirm'] = 'Confirmar';
dict['pt-BR']['Cancel'] = 'Cancelar';
dict['pt-BR']['Save port failed, please check the camps'] = 'Falhou ao salvar a porta. Por favor, cheque os campos';
dict['pt-BR']['Update this port?'] = 'Editar esta porta?';
dict['pt-BR']['Save this port?'] = 'Salvar esta porta?';
dict['pt-BR']['Please select a device'] = 'Por favor, selecione um dispositivo';
dict['pt-BR']['Please select a network'] = 'Por favor, selecione uma rede';
dict['pt-BR']['Please insert a name'] = 'Por favor, insira um nome';
dict['pt-BR']['Please insert a URN'] = 'Por favor, insira uma URN';
dict['pt-BR']['In Vlan: Missing argument after \"-\".<br>Sintax samples:<br>200<br>200-300<br>200-300,800-990'] = 'Em Vlan: Falta o argumento após \"-\".<br>Exemplo de sintaxe:<br>200<br>200-300<br>200-300,800-990';
dict['pt-BR']['\" is not a valid character.<br>Sintax samples:<br>200<br>200-300<br>200-300,800-990'] = '\" não é um caractere válido.<br>Exemplo de sintaxe:<br>200<br>200-300<br>200-300,800-990';
dict['pt-BR']['In Vlan: \"'] = 'Em Vlan: \"';
dict['pt-BR']['Please insert a Vlan'] = 'Por favor, insira uma Vlan';
dict['pt-BR']['Please insert a valid value for max capacity'] = 'Por favor, insira um valor válido para capacidade máxima';
dict['pt-BR']['Please insert a valid value for min capacity'] = 'Por favor, insira um valor válido para capacidade mínima';
dict['pt-BR']['Please insert a valid value for granularity'] = 'Por favor, insira um valor válido para granularidade';
dict['pt-BR']['This operation is not allowed'] = 'Está operação não está habilitada';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR' && dict['pt-BR'][obj]) return dict['pt-BR'][obj];
	else return obj;
}