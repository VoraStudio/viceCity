const isHome = /(^|\/)(index\.html)?$/.test(location.pathname);

export const home = isHome ? "" : "index.html";
export const logoHref = isHome ? "#inici" : "index.html";
