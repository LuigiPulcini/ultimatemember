<?php

	/***
	***	@profile name update
	***/
	add_action( 'um_update_profile_full_name', 'um_update_profile_full_name' );
	function um_update_profile_full_name( $changes ) {
		// Sync display name changes
		$option = UM()->options()->get( 'display_name' );
		
		$user_id = UM()->user()->id;

		if( ! isset( $user_id ) || empty( $user_id ) ){
			$user = get_user_by( 'email', $changes['user_email'] );
			um_fetch_user( $user->ID );
			$user_id = $user->ID;
		}
		
		switch ( $option ) {
			default:
				break;
			case 'full_name':
				$update_name = get_user_meta( $user_id, 'first_name', true ) . ' ' . get_user_meta( $user_id, 'last_name', true );
				break;
			case 'sur_name':
				$fname = get_user_meta( $user_id, 'first_name', true );
				$lname = get_user_meta( $user_id, 'last_name', true );
				$update_name = $lname . ' ' . $fname;
				break;
			case 'initial_name':
				$fname = get_user_meta( $user_id, 'first_name', true );
				$lname = get_user_meta( $user_id, 'last_name', true );
				$update_name = $fname . ' ' . ( !empty( $lname ) ? $lname[0] : '' );
				break;
			case 'initial_name_f':
				$fname = get_user_meta( $user_id, 'first_name', true );
				$lname = get_user_meta( $user_id, 'last_name', true );
				$update_name = ( !empty($fname) ? $fname[0] : '' ) . ' ' . $lname;
				break;
			case 'nickname':
				$update_name = get_user_meta( $user_id, 'nickname', true );
				break;
		}

		if ( isset( $update_name ) ) {
			
			$arr_user =  array( 'ID' => $user_id, 'display_name' => $update_name );
			$return = wp_update_user( $arr_user );

			if( is_wp_error( $return ) ) {
				wp_die(  '<pre>' . var_export( array( 'message' => $return->get_error_message(), 'dump' => $arr_user, 'changes' => $changes ), true ) . '</pre>'  );
			}
			

		}

		if ( isset( $changes['first_name'] ) && isset( $changes['last_name'] ) ) {
			
			$full_name = UM()->user()->profile['display_name'];
			$full_name = UM()->validation()->safe_name_in_url( $full_name );

			update_user_meta( UM()->user()->id, 'full_name', $full_name );

			
		}
		
		// regenerate slug
        UM()->user()->get_profile_url( $user_id, true );

	}