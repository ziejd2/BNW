function [ bnet ] = getParams( bnet, cases )
%getParams Code to initialize CPT and do parameter learning.
%This will be very basic for now.  I can add more options later.

dnodes = bnet.dnodes;
cnodes = bnet.cnodes;
nnodes = size(dnodes,2)+size(cnodes,2);

%make dnodes tabular_CPT
for i = 1:size(dnodes,2)
    bnet.CPD{dnodes(i)} = tabular_CPD(bnet,dnodes(i));
end

for i = 1:size(cnodes,2)
    bnet.CPD{cnodes(i)} = gaussian_CPD(bnet,cnodes(i));
end

bnet = learn_params(bnet,cases);


end

