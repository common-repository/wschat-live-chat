<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php esc_attr_e( 'Email feedback' ); ?></title>
</head>

<body style="font-family: 'Roboto', sans-serif; background: #F5F5F5;line-height:1.3">
	<div>
		<div style="background:#fff; padding: 20px; max-width:650px;width:80%; margin: 10px auto; font-weight: 500;  ">
			<table style="width:100%">
				<tr>
					<td>
						<table style="width:100%">
							<tr>
								<td>
								<h1 style="font-size:28px; font-weight:600 ;text-align: center;color: #101010;">Hi <?php esc_attr_e( $name ); ?>,<br><?php esc_attr_e( 'We Hope Your Query was Resolved.' ); ?></h1>
								</td>
							</tr>

							<tr height="30"></tr>

							<tr>
								<td>
									<h2 style="font-size:24px; font-weight:400 ;text-align: center;color: #101010;">
										<?php esc_attr_e( 'Please Take a Moment to Give Us a Feedback.' ); ?>
									</h2>
								</td>
							</tr>

							<tr height="80"></tr>

							<tr>
								<td>
									<h2 style="font-size:24px; font-weight:400 ;text-align: center;color: #101010;">Tell
										<?php esc_attr_e( 'Us About Your Experience.' ); ?>
										</h2>
								</td>
							</tr>

							<tr>

								<td style="text-align:center">
								<a href="<?php echo esc_url( add_query_arg( 'wschat_email_feedback', 'bad', site_url() ) ); ?>"
										style="text-decoration:none;display:inline-block;width: 193px;border: 1px solid #707070;color:#707070;background:#fff;border-radius:5px;font-size:24px; padding: 20px;margin:10px">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
											viewBox="0 0 16.266 16.5" style="margin-right: 20px">
											<path id="Icon_feather-thumbs_down" data-name="Icon feather-thumbs down"
												d="M7.262,11.25v3a2.25,2.25,0,0,0,2.25,2.25l3-6.75V1.5H4.052a1.5,1.5,0,0,0-1.5,1.275L1.517,9.525a1.5,1.5,0,0,0,1.5,1.725Zm5.25-9.75h2.25a1.5,1.5,0,0,1,1.5,1.5V8.25a1.5,1.5,0,0,1-1.5,1.5h-2.25"
												transform="translate(-0.746 -0.75)" fill="none" stroke="#707070"
												stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
												</svg>
<?php esc_attr_e( 'Bad' ); ?>
									</button>

								<a href="<?php echo esc_url( add_query_arg( 'wschat_email_feedback', 'good', site_url() ) ); ?>"
										style="text-decoration:none;display:inline-block;width: 193px;border: 1px solid #2489DB;color:#fff;background:#2489DB;border-radius:5px;font-size:24px; padding: 20px;margin:10px">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
											viewBox="0 0 16.266 16.5" style="margin-right: 20px">
											<path id="Icon_feather-thumbs-up" data-name="Icon feather-thumbs-up"
												d="M10.5,6.75v-3A2.25,2.25,0,0,0,8.25,1.5l-3,6.75V16.5h8.46a1.5,1.5,0,0,0,1.5-1.275l1.035-6.75a1.5,1.5,0,0,0-1.5-1.725ZM5.25,16.5H3A1.5,1.5,0,0,1,1.5,15V9.75A1.5,1.5,0,0,1,3,8.25H5.25"
												transform="translate(-0.75 -0.75)" fill="none" stroke="#fff"
												stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
										</svg>
<?php esc_attr_e( 'Good' ); ?>
									</button>
								</td>

							</tr>
							<tr style="height: 50px"></tr>
							<tr>
							<td><h2 style="font-size:24px; font-weight:400 ;text-align: center;color: #101010;"><?php esc_attr_e( 'Thank You for Your Time.' ); ?></h2></td>
							</tr>
							<tr style="height: 50px"></tr>
							<tr>
								<td style="text-align:center">
								<p style="font-size: 14px;color:#101010;margin:0"><?php esc_attr_e( 'Powered By' ); ?>: <a style="color:#2489DB; text-decoration: underline;">WSChat</a></p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>
