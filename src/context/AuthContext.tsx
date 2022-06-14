import React from 'react';

const ContextoUsuario = React.createContext({});

export const ProviderUsuario = ContextoUsuario.Provider;
export const ConsumerUsuario = ContextoUsuario.Consumer;

export default ContextoUsuario;