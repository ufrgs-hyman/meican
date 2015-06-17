var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Request will be accepted. If you want, provide a message:'] = 'A requisição vai ser aceita. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['Request will be rejected. If you want, provide a message:'] = 'A requisição vai ser rejeitada. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['All request will be accepted. If you want, provide a message:'] = 'Todas as requisições serão aceitar. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['All request will be accepted. If you want, provide a message:'] = 'Todas as requisições serão rejeitadas. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['Cancel'] = 'Cancelar';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR') return dict['pt-BR'][obj];
	else return obj;
}