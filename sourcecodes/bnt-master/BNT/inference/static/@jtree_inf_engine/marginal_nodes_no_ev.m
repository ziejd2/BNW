function marginal = marginal_nodes_no_ev(bnet,engine, query)
% MARGINAL_NODES Get original distribution of the specified query nodes (jtree)
% marginal = marginal_nodes(bnet, engine, query)
%

if ismember(query,bnet.dnodes)
  marginal.domain = query;
  marginal.T = CPD_to_CPT_orig(bnet.CPD{query});
  marginal.mu = [];
  marginal.Sigma = [];
else
  c = clq_containing_nodes(engine, query);
  if c == -1
    error(['no clique contains ' num2str(query)]);
  end
  marginal = pot_to_marginal(marginalize_pot(engine.clpot{c}, query, engine.maximize));
end

