$.jsonRPC.setup({
  endPoint: 'http://fetal.em70.ru/proto/api/web/index.php?r=rpc'
});

$.jsonRPC.request('ef-get-token', {
  params: {
    'login': 'login',
    'password': 'password'
  }
})
