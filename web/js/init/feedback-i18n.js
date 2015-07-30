var fbdict = [];
fbdict['pt-BR'] = [];

fbdict['pt-BR']['Describe your idea'] = 'Descreva sua idéia';
fbdict['pt-BR']['Describe your question'] = 'Descreva sua dúvida';
fbdict['pt-BR']['Describe your praise'] = 'Descreva seu elogio';
fbdict['pt-BR']['Describe your problem'] = 'Descreva seu problema';
fbdict['pt-BR']['Problems to send, try again later'] = 'Houver um problema no envio, tente novamente mais tarde';
fbdict['pt-BR']['Please, enter a message.'] = 'Por favor, insira uma mensagem.';
fbdict['pt-BR']['Sad'] = 'Triste';
fbdict['pt-BR']['Indifferent'] = 'Indiferente';
fbdict['pt-BR']['Silly'] = 'Bobo';
fbdict['pt-BR']['Happy'] = 'Feliz';

function fbtt(obj){
	if(language == 'pt-BR' && fbdict['pt-BR'][obj]) return fbdict['pt-BR'][obj];
	else return obj;
}