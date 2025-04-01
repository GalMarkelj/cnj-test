const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"index":{"uri":"\/","methods":["GET","HEAD"]},"housing.document.upload":{"uri":"\/","methods":["POST"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
