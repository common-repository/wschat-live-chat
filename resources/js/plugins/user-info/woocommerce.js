import { useState, useEffect } from "@wordpress/element";

export const WooOrders = props => {
	const [all_orders_link, setAllOrdersLink] = useState('');
	const [orders, setOrders] = useState([]);
	const [summary, setSummary] = useState({});

	const getOrderSummary = () => {
        const data = {
        	conversation_id: props.conversation.id,
        	action: 'wschat_admin_wc_get_orders_summary'
        };

        jQuery.post(wschat_ajax_obj.ajax_url, data, (res) => {
        	setOrders(res.data.orders);
        	setSummary(res.data.summary);
        	setAllOrdersLink(res.data.all_orders_link);
        }).fail(f => {
        	setOrders([]);
        });
	};

	useEffect(() => {
	    getOrderSummary();
	}, [props.conversation.user]);

//     if (orders.length === 0) {
//         return '';
//     }

	return (
		<div>
		    {orders.length ?
		    <div className="border-0 border-bottom border-secondary mb-2">
			    <div className="d-flex justify-content-between">
				    <h6>Orders</h6>
				    <a href={all_orders_link} target="_blank" className="text-primary">
					    See all <i className="fa-solid fa-angles-right ms-1"></i>
				    </a>
			    </div>
			    {orders.map(order => <OrderItem key={order.id} order={order} />)}
		    </div>
		    : '' }
		    { summary.length ? <CustomerValue summary={summary}/> : '' }
		</div>
	);
}

export const OrderItem = props => {
    return (
		<div
			className="mb-1 shadow-sm rounded-3 px-3 py-1 d-flex justify-content-between align-items-center position-relative elex-ws-chat-order">
			<div>
				<a href={props.order.link} target="_blank" >#{props.order.id}</a>
				<div className="xs">{props.order.date}</div>
			</div>
			<h6><span dangerouslySetInnerHTML={{__html: props.order.currency_symbol}}></span> {props.order.total} {props.order.currency}</h6>

			<div
				className="position-absolute w-100 rounded-3 bg-white border border-secondary p-2   elex-ws-chat-hover-order">
				<div className="d-flex justify-content-between mb-1">
				    <a href={props.order.link} target="_blank" >#{props.order.id}</a>
					<div className="badge bg-success bg-opacity-50">{props.order.status}</div>
				</div>
				<div className="d-flex gap-1 ">
					{props.order.products.slice(0, 5).map(product => {
					    return (
					        <div key={product.id} className="elex-ws-chat-xs-profile-pic">
						        <div className="ratio ratio-4x3 rounded-3 overflow-hidden">
							        <img src={product.thumbnail} title={product.thumbnail} alt={product.thumbnail}/>
						        </div>
					        </div>
					    );
					})}
					{props.order.products.length > 5 ? <div className="badge text-primary">{props.order.products.length - 5} more</div> : ''}
				</div>
			</div>
		</div>
    );
}

export const CustomerValue = props => {
    return (
		<div className="mb-2 border-0 border-bottom border-secondary">
			<h6>Customer Value</h6>
			<div className="shadow-sm px-3 d-flex gap-3 rounded-3">
				<div className="elex-ws-chat-xl-profile-pic">
					<div
						className="ratio ratio-1x1 rounded-circle border border-3 border-primary overflow-hidden">
						<div className="text-center d-flex align-items-center justify-content-center">
							<div>
								<p className="mb-0">Total Orders</p>
								<h6 className="mb-0">{props.summary.total_orders}</h6>
							</div>

						</div>
					</div>
				</div>


				<div>
					<div className="mb-3 row">
						<div className="col-9">Delivered</div>
						<div className="col-3">
							<h6 className="mb-0">{props.summary.completed_orders}</h6>
						</div>
					</div>

					<div className="mb-3 row">
						<div className="col-9">Return/Cancelled</div>
						<div className="col-3">
							<h6 className="mb-0">{props.summary.cancelled_orders}</h6>
						</div>
					</div>

					<div className="mb-3 row">
						<div className="col-9">Total Revenue</div>
						<div className="col-3">
							<h6 className="mb-0"><span dangerouslySetInnerHTML={{__html: props.summary.currency_symbol}}></span>{props.summary.total_revenue }</h6>
						</div>
					</div>
				</div>
			</div>

		</div>
    );
}
